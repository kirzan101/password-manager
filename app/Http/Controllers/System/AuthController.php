<?php

namespace App\Http\Controllers\System;

use App\DTOs\ChangePasswordDTO;
use App\DTOs\FirstLoginChangePasswordDTO;
use App\Helpers\ErrorHelper;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\System\ChangePasswordFormRequest;
use App\Interfaces\ActivityLoggerInterface;
use App\Interfaces\AuthInterface;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\ManageAccountInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function __construct(
        private AuthInterface $auth,
        private ManageAccountInterface $manageAccount,
        private CurrentUserInterface $currentUser,
        private ActivityLoggerInterface $activityLogger
    ) {}

    /**
     * show login page
     */
    public function index()
    {
        return Inertia::render('Login');
    }

    /**
     * attempt login
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $loginResult = $this->auth->login($request->toArray());

        if ($loginResult->code >= 400 && $loginResult->code < 500) {
            return back()->withErrors([
                'error' => $loginResult->message,
            ]);
        }

        // Set a global version for permissions caching if it doesn't exist
        Cache::add("permissions.version.global", 1, now()->addYears(10));

        // Log the successful login activity
        $this->activityLogger->addLog($loginResult, $request, 'auth', 'login', $this->currentUser->getProfileId());

        return redirect()->intended();
    }

    /**
     * attempt logout
     */
    public function logout()
    {
        // Clear the user's profile permissions version cache on logout
        $profileId = $this->currentUser->getProfileId();
        if ($profileId) {
            Cache::forget("permissions.version.$profileId");
        }

        $logoutResult = $this->auth->logout();

        if ($logoutResult->code != 200) {
            return back()->withErrors([
                'error' => $logoutResult->message,
            ]);
        }

        // Log the successful logout activity
        $request = request(); // Get the current request instance
        $request->merge(['user_id' => $this->currentUser->getUserId()]); // Add username to the request for logging
        $this->activityLogger->addLog($logoutResult, $request, 'auth', 'logout', $profileId);

        return redirect()->route('login');
    }

    /**
     * Change the user's password.
     */
    public function changePassword(ChangePasswordFormRequest $request)
    {
        // Prefer using request->filled() over exists() for clarity
        $profileId = $request->filled('profile_id')
            ? $request->input('profile_id')
            : Auth::user()->profile->id;

        // Merge the resolved profile_id back into the request so DTO sees it
        $request->merge(['profile_id' => $profileId]);

        // Build the DTO from the request
        $changePasswordDTO = ChangePasswordDTO::fromArray($request->toArray());

        // Call service
        $changePasswordResult = $this->manageAccount->changeUserProfilePassword($changePasswordDTO);

        // Normalize error message for production
        $productionErrorMessage = ErrorHelper::productionErrorMessage($changePasswordResult->code, $changePasswordResult->message);

        // Handle error
        if ($changePasswordResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code'    => $changePasswordResult->code,
                'message' => $productionErrorMessage,
            ]);
        }

        // Log the successful password change activity
        $this->activityLogger->addLog($changePasswordResult, $request, 'auth', 'change_password', $profileId);

        // Success → redirect back with flash message
        return redirect()->back()->with($changePasswordResult->status, $changePasswordResult->message);
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(int $userId)
    {
        $resetResult = $this->manageAccount->resetPassword($userId);

        // Normalize error message for production
        $productionErrorMessage = ErrorHelper::productionErrorMessage($resetResult->code, $resetResult->message);

        // Handle error
        if ($resetResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code'    => $resetResult->code,
                'message' => $productionErrorMessage,
            ]);
        }

        // Log the successful password reset activity
        $request = request(); // Get the current request instance
        $request->merge(['user_id' => $userId]); // Add user_id to the request for logging
        $this->activityLogger->addLog($resetResult, $request, 'auth', 'reset_password', $this->currentUser->getProfileId());

        // Success → redirect back with flash message
        return redirect()->back()->with($resetResult->status, $resetResult->message);
    }

    /**
     * Set the user's active status.
     */
    public function setUserStatus(int $userId)
    {
        $statusResult = $this->manageAccount->setUserActiveStatus($userId);

        // Normalize error message for production
        $productionErrorMessage = ErrorHelper::productionErrorMessage($statusResult->code, $statusResult->message);

        // Handle error
        if ($statusResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code'    => $statusResult->code,
                'message' => $productionErrorMessage,
            ]);
        }

        // Log the successful user status change activity
        $request = request(); // Get the current request instance
        $request->merge(['user_id' => $userId]); // Add user_id to the request for logging
        $this->activityLogger->addLog($statusResult, $request, 'auth', 'set_user_status', $this->currentUser->getProfileId());

        // Success → redirect back with flash message
        return redirect()->back()->with($statusResult->status, $statusResult->message);
    }

    /**
     * Show the first login page.
     */
    public function firstLogin()
    {
        // redirect to dashboard if user is not on first login
        if (!$this->currentUser->isFirstLogin()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('FirstLogin');
    }

    /**
     * Handle the first login password change.
     */
    public function firstLoginChangePassword(Request $request)
    {
        // Validate the request
        $request->validate([
            'password' => 'required|string|min:8|max:50|confirmed', // this validation combine password + password_confirmation in one rule
        ]);

        // Build the DTO from the request
        $firstLoginChangePasswordDTO = FirstLoginChangePasswordDTO::fromArray($request->toArray());
        $firstLoginChangePasswordDTO = $firstLoginChangePasswordDTO->withProfile($this->currentUser->getProfileId());

        // Call service
        $changePasswordResult = $this->manageAccount->firstLoginChangePassword($firstLoginChangePasswordDTO);

        // Normalize error message for production
        $productionErrorMessage = ErrorHelper::productionErrorMessage($changePasswordResult->code, $changePasswordResult->message);

        // Handle error
        if ($changePasswordResult->status === Helper::ERROR) {
            return Inertia::render('Error', [
                'code'    => $changePasswordResult->code,
                'message' => $productionErrorMessage,
            ]);
        }

        // Log the successful first login password change activity
        // add profile_id to the request for logging
        $request->merge(['profile_id' => $this->currentUser->getProfileId()]);
        $this->activityLogger->addLog($changePasswordResult, $request, 'auth', 'first_login_change_password', $this->currentUser->getProfileId());

        // Success → redirect to dashboard with flash message
        return redirect()->route('dashboard')->with($changePasswordResult->status, $changePasswordResult->message);
    }
}
