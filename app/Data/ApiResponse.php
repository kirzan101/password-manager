<?php

namespace App\Data;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

final class ApiResponse implements Responsable
{
    public function __construct(
        public readonly array $data,
        public readonly int $code = 200,
        public readonly array $headers = [],
        public readonly int $options = JSON_INVALID_UTF8_SUBSTITUTE,
    ) {}

    public function toResponse($request): JsonResponse
    {
        return response()->json(
            $this->data,
            $this->code,
            $this->headers,
            $this->options
        );
    }
}
