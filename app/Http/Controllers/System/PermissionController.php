<?php

namespace App\Http\Controllers\System;

use App\DTOs\PermissionDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionFormRequest;
use App\Interfaces\ActivityLoggerInterface;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Interfaces\PermissionInterface;
use App\Models\Permission;
use App\Traits\ReturnMessageTrait;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PermissionController extends Controller
{
    use ReturnMessageTrait;

    public function __construct(
        private PermissionInterface $permission,
        private PermissionFetchInterface $permissionFetch,
        private ActivityLoggerInterface $activityLogger
    ) {}

    const MODULE_NAME = 'permissions';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::denies('view', new Permission())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to view this page.'
            ]);
        }

        $fetchResult = $this->permissionFetch->permissionsByModule();
        $permissionsByModule = $fetchResult->data ?? [];

        return Inertia::render('System/Permissions', [
            'permissionsByModule' => $permissionsByModule,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionFormRequest $request)
    {
        // Verify if the current user has permission to create
        if (Gate::denies('create', new Permission())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to create this permission.'
            ]);
        }

        $permissionDTO = PermissionDTO::fromRequest($request);
        $result = $this->permission->storePermission($permissionDTO);

        // Log the activity
        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'store');

        return $this->returnMessage($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionFormRequest $request, int $id)
    {
        // Verify if the current user has permission to update
        if (Gate::denies('update', new Permission())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to update this permission.'
            ]);
        }

        $permissionDTO = PermissionDTO::fromRequest($request);
        $result = $this->permission->updatePermission($permissionDTO, $id);

        // Log the activity
        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'update');

        return $this->returnMessage($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        // Verify if the current user has permission to delete
        if (Gate::denies('delete', new Permission())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to delete this permission.'
            ]);
        }

        $result = $this->permission->deletePermission($id);

        // Clone the current HTTP request to avoid modifying the original request object,
        // then add (merge) the 'id' parameter into the cloned request.
        $request = clone request();
        $request->merge(['id' => $id]);

        // Log the activity
        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'delete');

        return $this->returnMessage($result);
    }
}
