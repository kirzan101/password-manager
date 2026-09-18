<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function __construct(
        protected \App\Interfaces\UserModuleInterface $userModule
    ) {}

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $modules = Cache::rememberForever('active_modules', function () {
            return Module::where('is_active', true)
                ->select('name', 'icon', 'route', 'category', 'order', 'base_name')
                ->get()
                ->toArray();
        });

        $auth = Auth::check()
            ? \App\Models\User::with('profile')->find(Auth::id())
            : null;

        $profile = $auth?->profile;
        $profileId = $auth?->profile?->id;
        $avatarUrl = $profile?->avatar ? route('uploads.show', ['path' => $profile->avatar]) : null;

        $nickName = str($profile?->nickname)?->title()->toString();

        return array_merge(parent::share($request), [
            'appVersion' => env('APP_VERSION', '1.0.0'),
            'appName' => env('APP_NAME', 'Laravel'),
            'appDeveloper' => env('APP_DEVELOPER', 'Developer'),
            'flash' => function () use ($request) {
                return [
                    // 'message' => $request->session()->get('message'), // ambiguous, use specific keys for different types of messages    
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                    'info' => $request->session()->get('info'),
                    'warning' => $request->session()->get('warning'),
                ];
            },
            'auth' => Auth::check() ? [
                'user' => [
                    // 'avatar' => $profile?->avatar ? \Illuminate\Support\Facades\Storage::url($profile->avatar) : null,
                    'avatar' => $avatarUrl,
                    'username' => $auth->username,
                    'name' => $profile?->getFullName(),
                    'first_name' => $profile?->first_name,
                    'last_name' => $profile?->last_name,
                    'nickname' => $nickName,
                    'initials' => $profile?->getInitials(),
                    'email' => $auth->email,
                    'position' => $profile?->position,
                    'isAdmin' => (bool) $auth->is_admin,
                    'isFirstLogin' => (bool) $auth->is_first_login,
                    'type' => $profile?->type,
                    'accessibleModules' => $this->userModule->getAccessibleModules($profileId),
                ],
            ] : null,
            'token' => $auth ? $auth->api_token : null,
            'modules' => $modules,
            'can' => $profileId ? $this->userModule->getAllPermissions($profileId) : [],
        ]);
    }
}
