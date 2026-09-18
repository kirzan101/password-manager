<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\DTOs\RoleDTO;
use App\Helpers\Helper;
use App\Interfaces\RoleInterface;
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
use App\Models\Role;

class RoleService implements RoleInterface
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
     * Store a new role in the database.
     *
     * @param RoleDTO $roleDTO
     * @return ModelResponse
     */
    public function storeRole(RoleDTO $roleDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($roleDTO) {

                $roleData = $roleDTO->toArray();
                $role = $this->base->store(Role::class, $roleData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Role created successfully!', $role, $role->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing role in the database.
     *
     * @param RoleDTO $roleDTO
     * @param int $roleId
     * @return ModelResponse
     */
    public function updateRole(RoleDTO $roleDTO, int $roleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($roleDTO, $roleId) {
                $role = $this->fetch->showQuery(Role::class, $roleId)->firstOrFail();

                $roleDTO = RoleDTO::fromModel($role, $roleDTO->toArray());

                $roleData = $roleDTO->toArray();
                $role = $this->base->update($role, $roleData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Role updated successfully!', $role, $roleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete the given role in the database.
     *
     * @param int $roleId
     * @return ModelResponse
     */
    public function deleteRole(int $roleId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($roleId) {
                $role = $this->fetch->showQuery(Role::class, $roleId)->firstOrFail();

                $this->base->delete($role);

                return ModelResponse::success(204, Helper::SUCCESS, 'Role deleted successfully!', null, $roleId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
