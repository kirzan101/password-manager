<?php

namespace App\Http\Controllers;

use App\Interfaces\ActivityLoggerInterface;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\FetchInterfaces\ProfileFetchInterface;
use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(
        private CurrentUserInterface $currentUser,
        private ProfileFetchInterface $profileFetch,
        private ActivityLoggerInterface $activityLogger
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Home', []);
    }

    /**
     * Display the profile page.
     */
    public function profile()
    {
        $profileId = $this->currentUser->getProfileId();
        $profileResult = $this->profileFetch->showProfile($profileId, ProfileResource::class);
        $profile = $profileResult->data;

        return Inertia::render('Profile', [
            'profile' => new ProfileResource($profile),
        ]);
    }
}
