<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Trait DetectsSoftDeletesTrait
 *
 * Provides a method to check if a model uses the SoftDeletes trait.
 */
trait DetectsSoftDeletesTrait
{
    /**
     * Determine if the given model class or instance uses SoftDeletes.
     *
     * @param object|string $model An Eloquent model instance or fully qualified class name.
     * @return bool True if the model uses SoftDeletes, false otherwise.
     */
    public function modelUsesSoftDeletes(object|string $model): bool
    {
        $modelInstance = is_string($model) ? new $model : $model;

        return in_array(SoftDeletes::class, class_uses_recursive(get_class($modelInstance)));
    }
}
