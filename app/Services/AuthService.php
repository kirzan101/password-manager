<?php

namespace App\Services;

use App\Data\ModelResponse;
use App\Helpers\Helper;
use App\Interfaces\AuthInterface;
use App\Models\User;
use App\Traits\EnsureSuccessTrait;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use RuntimeException;

class AuthService implements AuthInterface
{
    use HttpErrorCodeTrait,
        ReturnModelTrait,
        EnsureSuccessTrait;

    /**
     * Log in a user using either username or email.
     *
     * @param array $request
     * @return ModelResponse
     */
    public function login(array $request): ModelResponse
    {
        try {
            $isLoggedIn = false;

            if (filter_var($request['username'], FILTER_VALIDATE_EMAIL)) {
                // Attempt authentication using email
                $isLoggedIn = Auth::attempt([
                    'email' => $request['username'],
                    'password' => $request['password'],
                ]);
            } else {
                // Attempt authentication using username
                $isLoggedIn = Auth::attempt([
                    'username' => $request['username'],
                    'password' => $request['password'],
                ]);
            }

            if ($isLoggedIn) {
                if (Auth::user()->status !== Helper::ACCOUNT_STATUS_ACTIVE) {
                    Auth::logout();
                    return ModelResponse::error(403, Helper::ERROR, 'Account is inactive.');
                }

                // Retrieve the user model from the database to ensure it's an Eloquent model
                $user = User::find(Auth::id());

                // Generate a new token
                $token = Str::random(60);
                $user->api_token = $token;
                $user->last_login_at = Carbon::now(); // Update last login time
                $user->save();

                Auth::getSession()->regenerate();

                return ModelResponse::success(200, Helper::SUCCESS, 'Logged in successfully!', Auth::user(), Auth::id());
            }

            return ModelResponse::error(422, Helper::ERROR, 'The provided credentials do not match our records.');
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Log out the currently authenticated user.
     *
     * @return ModelResponse
     */
    public function logout(): ModelResponse
    {
        try {
            if (Auth::check()) {

                // Invalidate the user's API token
                $user = User::find(Auth::id());
                $user->api_token = null;
                $user->last_login_at = Carbon::now(); // Update last login time
                $user->save();

                Auth::logout();

                // Use the facade for testability
                Session::invalidate();
                Session::regenerateToken();

                return ModelResponse::success(200, Helper::SUCCESS, 'Logged out successfully!');
            }

            return ModelResponse::error(401, Helper::ERROR, 'No authenticated user to log out.');
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Get the API token of the currently authenticated user.
     *
     * @return string|null
     */
    public function getToken(): ?string
    {
        return Auth::user() ? Auth::user()->api_token : null;
    }
}
