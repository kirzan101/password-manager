<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\ModuleDTO;

interface ModuleInterface
{
    /**
     * Store a new module in the database.
     *
     * @param  ModuleDTO $moduleDTO
     * @return ModelResponse
     */
    public function storeModule(ModuleDTO $moduleDTO): ModelResponse;

    /**
     * Update an existing module in the database.
     *
     * @param  ModuleDTO $moduleDTO
     * @param  int    $moduleId
     * @return ModelResponse
     */
    public function updateModule(ModuleDTO $moduleDTO, int $moduleId): ModelResponse;

    /**
     * Delete the given module in the database.
     *
     * @param  int  $moduleId
     * @return ModelResponse
     */
    public function deleteModule(int $moduleId): ModelResponse;
}
