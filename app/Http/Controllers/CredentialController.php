<?php

namespace App\Http\Controllers;

use App\DTOs\CredentialDTO;
use App\DTOs\ManageCredentialDTO;
use App\Http\Requests\CredentialFormRequest;
use App\Interfaces\ActivityLoggerInterface;
use App\Interfaces\CredentialInterface;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\ManageCredentialInterface;
use App\Models\Credential;
use App\Traits\ReturnMessageTrait;
use App\Traits\ReturnModulePermissionTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CredentialController extends Controller
{
    use ReturnMessageTrait;

    public function __construct(
        // private CredentialInterface $credential,
        private CurrentUserInterface $currentUser,
        private ManageCredentialInterface $credential,
        private ActivityLoggerInterface $activityLogger
    ) {}

    const MODULE_NAME = 'credentials';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::denies('view', new Credential())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to view this page.'
            ]);
        }

        return Inertia::render('System/Credentials', []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CredentialFormRequest $request)
    {
        // Verify if the current user has permission to create
        if (Gate::denies('create', new Credential())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to create this credential.'
            ]);
        }

        // $credentialDTO = CredentialDTO::fromRequest($request);
        $manageCredentialDTO = ManageCredentialDTO::fromArray([
            'owner_profile_id' => $this->currentUser->getProfileId(),
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'secret' => $request->input('secret'),
            'is_favorite' => false
        ]);
        $result = $this->credential->storeNewCredential($manageCredentialDTO);

        // Log the activity
        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'store');

        return $this->returnMessage($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CredentialFormRequest $request, int $id)
    {
        // Verify if the current user has permission to update
        if (Gate::denies('update', new Credential())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to update this credential.'
            ]);
        }

        $manageCredentialDTO = ManageCredentialDTO::fromRequest($request);
        $result = $this->credential->updateCredential($manageCredentialDTO, $id);

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
        if (Gate::denies('delete', new Credential())) {
            return Inertia::render('Error', [
                'code' => 403,
                'message' => 'You do not have permission to delete this credential.'
            ]);
        }

        $result = $this->credential->deleteCredential($id);

        // Clone the current HTTP request to avoid modifying the original request object,
        // then add (merge) the 'id' parameter into the cloned request.
        $request = clone request();
        $request->merge(['id' => $id]);

        // Log the activity
        $this->activityLogger->addLog($result, $request, self::MODULE_NAME, 'delete');

        return $this->returnMessage($result);
    }
}
