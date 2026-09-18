<?php

namespace App\Http\Controllers\System;

use App\DTOs\UserGroupDTO;
use App\Helpers\ErrorHelper;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserGroupFormRequest;
use App\Interfaces\ActivityLoggerInterface;
use App\Interfaces\FetchInterfaces\PermissionFetchInterface;
use App\Interfaces\FetchInterfaces\UserGroupFetchInterface;
use App\Interfaces\UserGroupInterface;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UserGroupController extends Controller
{
    public function __construct(
        private UserGroupInterface $userGroup,
        private ActivityLoggerInterface $activityLogger
    ) {}

    const MODULE_NAME = 'user_groups';

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // verify the current user's group permissions
        if (Gate::denies('view', new UserGroup())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => ErrorHelper::productionErrorMessage(403, 'You do not have permission to view this page.')
            ]);
        }

        return Inertia::render('System/UserGroups', [
            'user_group_types' => Helper::USER_GROUP_CODE_TYPES
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserGroupFormRequest $request)
    {
        // Verify if the current user's group has permission to create
        if (Gate::denies('create', new UserGroup())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to create user group.'
            ]);
        }

        $userGroupDTO = UserGroupDTO::fromArray($request->all());
        $storeResult = $this->userGroup->storeUserGroup($userGroupDTO);

        // Log the activity
        $this->activityLogger->addLog($storeResult, $request, self::MODULE_NAME, 'store');

        $productionErrorMessage = ErrorHelper::productionErrorMessage($storeResult->code, $storeResult->message);
        if ($storeResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $storeResult->code,
                'message' => $productionErrorMessage
            ]);
        }

        return redirect()->back()->with($storeResult->status, $productionErrorMessage);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserGroupFormRequest $request, int $id)
    {
        // Verify if the current user's group has permission to update
        if (Gate::denies('update', new UserGroup())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to update user group.'
            ]);
        }

        $userGroupDTO = UserGroupDTO::fromArray($request->all());
        $updateResult = $this->userGroup->updateUserGroup($userGroupDTO, $id);

        // Log the activity
        $this->activityLogger->addLog($updateResult, $request, self::MODULE_NAME, 'update');

        $productionErrorMessage = ErrorHelper::productionErrorMessage($updateResult->code, $updateResult->message);
        if ($updateResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $updateResult->code,
                'message' => $productionErrorMessage
            ]);
        }

        return redirect()->back()->with($updateResult->status, $productionErrorMessage);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        // Verify if the current user's group has permission to delete
        if (Gate::denies('delete', new UserGroup())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to delete this user group.'
            ]);
        }

        $deleteResult = $this->userGroup->deleteUserGroup($id);

        // add the id to the request for logging purposes
        $request = clone request();
        $request->merge(['id' => $id]);
        $this->activityLogger->addLog($deleteResult, $request, self::MODULE_NAME, 'delete');

        $productionErrorMessage = ErrorHelper::productionErrorMessage($deleteResult->code, $deleteResult->message);
        if ($deleteResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code' => $deleteResult->code,
                'message' => $productionErrorMessage
            ]);
        }

        return redirect()->back()->with($deleteResult->status, $productionErrorMessage);
    }
}
