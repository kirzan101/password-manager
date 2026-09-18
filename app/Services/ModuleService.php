<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\DTOs\ModuleDTO;
use App\Helpers\Helper;
use App\Interfaces\ModuleInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\EnsureDataTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;
use App\Models\Module;
use Illuminate\Support\Str;

class ModuleService implements ModuleInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait,
        EnsureSuccessTrait,
        EnsureDataTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new module in the database.
     *
     * @param ModuleDTO $moduleDTO
     * @return ModelResponse
     */
    public function storeModule(ModuleDTO $moduleDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($moduleDTO) {

                // Ensure that the order is set, if not, default to 0
                if (empty($moduleDTO->order)) {
                    $moduleDTO = $moduleDTO->withOrder(0);
                }

                // Ensure that the base_name is set, if not, generate it from the name
                if (empty($moduleDTO->base_name) && !empty($moduleDTO->name)) {
                    $base_name = strtolower(Str::plural(Str::snake($moduleDTO->name))); // ensure plural and snake case. e.g "User Group" => "user_groups"
                    $moduleDTO = $moduleDTO->withBaseName($base_name);
                }

                $moduleData = $moduleDTO->toArray();
                $module = $this->base->store(Module::class, $moduleData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Module created successfully!', $module, $module->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing module in the database.
     *
     * @param ModuleDTO $moduleDTO
     * @param int $moduleId
     * @return ModelResponse
     */
    public function updateModule(ModuleDTO $moduleDTO, int $moduleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($moduleDTO, $moduleId) {
                $module = $this->fetch->showQuery(Module::class, $moduleId)->firstOrFail();

                $moduleDTO = ModuleDTO::fromModel($module, $moduleDTO->toArray());

                // Ensure that the base_name is set, if not, generate it from the name
                if (empty($moduleDTO->base_name) && !empty($moduleDTO->name)) {
                    $base_name = strtolower(Str::plural(Str::snake($moduleDTO->name))); // ensure plural and snake case. e.g "User Group" => "user_groups"
                    $moduleDTO = $moduleDTO->withBaseName($base_name);
                }

                $moduleData = $moduleDTO->toArray();
                $module = $this->base->update($module, $moduleData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Module updated successfully!', $module, $moduleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given module in the database.
     *
     * @param int $moduleId
     * @return ModelResponse
     */
    public function deleteModule(int $moduleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($moduleId) {
                $module = $this->fetch->showQuery(Module::class, $moduleId)->firstOrFail();

                $this->base->delete($module);

                return ModelResponse::success(204, Helper::SUCCESS, 'Module deleted successfully!', null, $moduleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
