<?php

namespace App\Data;

use App\Helpers\Helper;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelResponse
 *
 * A response class that encapsulates a single model instance.
 *
 * @package App\Data
 * @property-read ?Model $data The model instance, instance of Illuminate\Database\Eloquent\Model.
 * @method static self success(int $code = 200, string $status = Helper::SUCCESS, string $message = 'Success', ?Model $data = null, ?int $lastId = null)
 * @method static self error(int $code = 400, string $status = Helper::ERROR, string $message = 'Error', ?Model $data = null, ?int $lastId = null)
 *
 * @param int $code The HTTP status code.
 * @param string $status The status of the response.
 * @param string $message The message associated with the response.
 * @param Model|null $data The model instance, instance of Illuminate\Database\Eloquent\Model.
 * @param int|null $lastId The ID of the last created or updated model.
 */
class ModelResponse extends BaseResponse
{
    /**
     * Constructor for ModelResponse.
     */
    public function __construct(
        int $code,
        string $status,
        string $message,
        public readonly ?Model $data = null,
        public readonly ?int $lastId = null
    ) {
        // call BaseResponse constructor
        parent::__construct($code, $status, $message);
    }

    /**
     * Create a success response.
     */
    public static function success(
        int $code = 200,
        string $status = Helper::SUCCESS,
        string $message = 'Success',
        ?Model $data = null,
        ?int $lastId = null
    ): self {
        return new self($code, $status, $message, $data, $lastId);
    }

    /**
     * Create an error response.
     */
    public static function error(
        int $code = 400,
        string $status = Helper::ERROR,
        string $message = 'Error',
        ?Model $data = null,
        ?int $lastId = null
    ): self {
        return new self($code, $status, $message, $data, $lastId);
    }

    /**
     * Convert the response to an array.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'last_id' => $this->lastId,
            'data' => $this->data,
        ]);
    }
}
