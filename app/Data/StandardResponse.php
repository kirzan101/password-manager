<?php

namespace App\Data;

use App\Helpers\Helper;

class StandardResponse extends BaseResponse
{
    /**
     * Constructor for StandardResponse.
     */
    public function __construct(
        int $code,
        string $status,
        string $message,
        public readonly mixed $data = null
    ) {
        parent::__construct($code, $status, $message);
    }

    /**
     * Create a success response.
     */
    public static function success(
        int $code = 200,
        string $status = Helper::SUCCESS,
        string $message = 'Success',
        mixed $data = null
    ): self {
        return new self($code, $status, $message, $data);
    }

    /**
     * Create an error response.
     */
    public static function error(
        int $code = 400,
        string $status = Helper::ERROR,
        string $message = 'Error',
        mixed $data = null
    ): self {
        return new self($code, $status, $message, $data);
    }

    /**
     * Convert the response to an array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'data' => $this->data,
        ]);
    }
}
