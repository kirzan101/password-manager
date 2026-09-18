<?php

namespace App\Data;

use Illuminate\Http\JsonResponse;

abstract class BaseResponse
{
    public function __construct(
        public readonly int $code,
        public readonly string $status,
        public readonly string $message
    ) {}

    /**
     * Convert the response to an array.
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'status' => $this->status,
            'message' => $this->message,
        ];
    }

    /**
     * Convert the response to a JsonResponse.
     */
    public function toResponse(): JsonResponse
    {
        return response()->json($this->toArray(), $this->code, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }
}
