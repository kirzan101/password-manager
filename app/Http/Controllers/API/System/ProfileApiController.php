<?php

namespace App\Http\Controllers\API\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Interfaces\FetchInterfaces\ProfileFetchInterface;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    public function __construct(private ProfileFetchInterface $profileFetch) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Set default pagination and sorting
        $request->merge([
            'per_page' => $request->input('per_page', 10),
            'sort_by' => $request->input('sort_by', 'id'),
            'sort' => $request->input('sort', 'desc'),
        ]);

        $results = $this->profileFetch->indexProfiles($request->toArray(), true, ProfileResource::class);

        $data = $results->data;
        $code = $results->code;
        $status = $results->status;
        $message = $results->message;

        return response()->json([
            'data' => ProfileResource::collection($data->all()),
            'per_page' => $data->perPage(),
            'current_page' => $data->currentPage(),
            'total' => $data->total(),
            'last_page' => $data->lastPage(),
            'search' => $request->input('search'),
            'sort_by' => $request->input('sort_by'),
            'sort' => $request->input('sort'),
            'code' => $code,
            'status' => $status,
            'message' => $message
        ], $code);
    }
}
