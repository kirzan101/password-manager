<?php

namespace App\Http\Controllers\API\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndexResource\UserGroupIndexResource;
use App\Http\Resources\UserGroupResource;
use App\Interfaces\FetchInterfaces\UserGroupFetchInterface;
use Illuminate\Http\Request;

class UserGroupApiController extends Controller
{
    public function __construct(private UserGroupFetchInterface $userGroupFetch) {}

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

        $results = $this->userGroupFetch->indexUserGroups($request->toArray(), true, UserGroupResource::class);

        $data = $results->data;
        $code = $results->code;
        $status = $results->status;
        $message = $results->message;

        return response()->json([
            'data' => UserGroupResource::collection($data->all()),
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

    /**
     * Display a listing of the resource.
     */
    public function searchIndex(Request $request)
    {
        // Set default pagination and sorting
        $request->merge([
            'per_page' => $request->input('per_page', 50),
            'sort_by' => $request->input('sort_by', 'name'),
            'sort' => $request->input('sort', 'asc'),
        ]);

        $results = $this->userGroupFetch->indexUserGroups($request->toArray(), true, UserGroupIndexResource::class);

        $data = $results->data;
        $code = $results->code;
        $status = $results->status;
        $message = $results->message;

        return response()->json([
            'data' => UserGroupIndexResource::collection($data->all()),
            'code' => $code,
            'status' => $status,
            'message' => $message
        ], $code);
    }
}
