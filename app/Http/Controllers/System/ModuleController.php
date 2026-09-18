<?php

namespace App\Http\Controllers\System;

use App\DTOs\ModuleDTO;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ModuleFormRequest;
use App\Interfaces\ActivityLoggerInterface;
use App\Interfaces\ModuleInterface;
use App\Models\Module;
use App\Traits\ReturnMessageTrait;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ModuleController extends Controller
{
    use ReturnMessageTrait;

    public function __construct(
        private ModuleInterface $module,
        private ActivityLoggerInterface $activityLogger
    ) {}

    const MODULE_NAME = 'modules';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::denies('view', new Module())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to view this page.'
            ]);
        }

        return Inertia::render('System/Modules', [
            'categories' => Helper::MODULE_CATEGORIES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ModuleFormRequest $request)
    {
        // Verify if the current user has permission to create
        if (Gate::denies('create', new Module())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to create this module.'
            ]);
        }

        $moduleDTO = ModuleDTO::fromRequest($request);
        $result = $this->module->storeModule($moduleDTO);

        // Log the activity
        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'store');

        return $this->returnMessage($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ModuleFormRequest $request, int $id)
    {
        // Verify if the current user has permission to update
        if (Gate::denies('update', new Module())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to update this module.'
            ]);
        }

        $moduleDTO = ModuleDTO::fromRequest($request);
        $result = $this->module->updateModule($moduleDTO, $id);

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
        if (Gate::denies('delete', new Module())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to delete this module.'
            ]);
        }

        $result = $this->module->deleteModule($id);

        // Clone the current HTTP request to avoid modifying the original request object,
        // then add (merge) the 'id' parameter into the cloned request.
        $request = clone request();
        $request->merge(['id' => $id]);

        // Log the activity
        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'delete');

        return $this->returnMessage($result);
    }
}
