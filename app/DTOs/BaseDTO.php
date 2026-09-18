<?php

namespace App\DTOs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use ReflectionClass;

abstract class BaseDTO
{
    public function __construct(
        public ?int $id = null,
    ) {}

    /**
     * Fields that should not be included in toArray().
     *
     * Override this in child DTOs when needed.
     */
    protected array $hidden = [];

    /**
     * Convert the DTO into an associative array.
     */
    public function toArray(): array
    {
        $data = get_object_vars($this);

        // Remove hidden fields
        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }

        // Also remove 'hidden' itself to avoid Mockery issues
        unset($data['hidden']);

        return $data;
    }

    /**
     * Create a new instance of the DTO from an array.
     */
    public static function fromArray(array $data): static
    {
        // Use PHP Reflection to create a new instance of the current class.
        // For each constructor parameter, get the value from $data by parameter name,
        // or use the parameter's default value if not provided in $data.
        // Then instantiate the class with these arguments.
        $reflection = new ReflectionClass(static::class);
        return $reflection->newInstanceArgs(
            array_map(
                function ($param) use ($data) {
                    $name = $param->getName();

                    if (array_key_exists($name, $data)) {
                        return $data[$name];
                    }

                    if ($param->isDefaultValueAvailable()) {
                        return $param->getDefaultValue();
                    }

                    return null; // fallback for non-optional params
                },
                $reflection->getConstructor()->getParameters()
            )
        );
    }

    /**
     * Create a DTO instance from a model with optional override data.
     *
     * Values from the provided data array take precedence over model attributes.
     * If neither contains a value, the constructor default value is used.
     */
    public static function fromModel(Model $model, array $data = []): static
    {
        $reflection = new ReflectionClass(static::class);

        $args = [];

        foreach ($reflection->getConstructor()->getParameters() as $param) {
            $name = $param->getName();

            // 1. from override data
            if (array_key_exists($name, $data) && $data[$name] !== null) {
                $args[$name] = $data[$name];
                continue;
            }

            // 2. from model
            if ($model->getAttribute($name) !== null) {
                $args[$name] = $model->getAttribute($name);
                continue;
            }

            // 3. default
            $args[$name] = $param->isDefaultValueAvailable()
                ? $param->getDefaultValue()
                : null;
        }

        return $reflection->newInstanceArgs(
            array_values($args)
        );
    }

    /**
     * Create a new instance of the DTO from an HTTP request.
     */
    public static function fromRequest(Request $request): static
    {
        return self::fromArray($request->all());
    }
}
