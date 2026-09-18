<?php

namespace App\Http\Controllers\System;

use App\DTOs\ManageRoleDTO;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoleFormRequest;
use App\Interfaces\ActivityLoggerInterface;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Interfaces\ManageRoleInterface;
use App\Interfaces\UserModuleInterface;
use App\Models\Role;
use App\Traits\ReturnMessageTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class RoleController extends Controller
{
    use ReturnMessageTrait;

    public function __construct(
        private UserModuleInterface $userModule,
        private PermissionFetchInterface $permissionFetch,
        private ManageRoleInterface $manageRole,
        private ActivityLoggerInterface $activityLogger
    ) {}

    const MODULE_NAME = 'roles';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::denies('view', new Role())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to view this page.'
            ]);
        }

        $permissions = Cache::remember('permission_fetch_list', 60, function () {
            // Fetch the result and extract only the 'data' part
            $result = $this->permissionFetch->indexPermissions();
            return $result->data ?? []; // Only return 'data' part
        });

        $moduleLists = Cache::remember('module_lists', 60, function () {
            return Helper::getModuleList();
        });

        return Inertia::render('System/Roles', [
            'permissions' => $permissions,
            'moduleLists' => $moduleLists,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleFormRequest $request)
    {
        // Verify if the current user has permission to create
        if (Gate::denies('create', new Role())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to create this role.'
            ]);
        }

        $manageRoleDTO = ManageRoleDTO::fromRequest($request);
        $result = $this->manageRole->storeRole($manageRoleDTO);

        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'store');

        $this->refreshCache(); // Refresh the cache after creating the role

        return $this->returnMessage($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleFormRequest $request, int $id)
    {
        // Verify if the current user has permission to update
        if (Gate::denies('update', new Role())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to update this role.'
            ]);
        }

        $manageRoleDTO = ManageRoleDTO::fromRequest($request);
        $result = $this->manageRole->updateRole($manageRoleDTO, $id);

        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'update');

        $this->refreshCache(); // Refresh the cache after updating the role

        return $this->returnMessage($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        // Verify if the current user has permission to delete
        if (Gate::denies('delete', new Role())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to delete this role.'
            ]);
        }

        $result = $this->manageRole->deleteRole($id);

        // Clone the current HTTP request to avoid modifying the original request object,
        // then add (merge) the 'id' parameter into the cloned request.
        $request = clone request();
        $request->merge(['id' => $id]);

        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'delete');

        $this->refreshCache(); // Refresh the cache after deleting the role

        return $this->returnMessage($result);
    }

    /**
     * Refresh the cache for the current user's permissions.
     */
    protected function refreshCache(): void
    {
        $this->userModule->refreshGlobalPermissionsVersion();
    }
}
