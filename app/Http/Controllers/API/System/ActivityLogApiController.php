<?php

namespace App\Http\Controllers\API\System;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndexResource\ActivityLogIndexResource;
use App\Http\Resources\ActivityLogResource;
use App\Interfaces\FetchInterfaces\ActivityLogFetchInterface;
use Illuminate\Http\Request;

class ActivityLogApiController extends Controller
{
    public function __construct(private ActivityLogFetchInterface $activityLogFetch) {}

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

        $result = $this->activityLogFetch->indexActivityLogs($request->toArray(), true, ActivityLogResource::class);

        $data = $result->data;
        $code = $result->code;
        $status = $result->status;
        $message = $result->message;

        return response()->json([
            'data' => ActivityLogResource::collection($data->all()),
            'additional_data' => [],
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

        $result = $this->activityLogFetch->indexActivityLogs($request->toArray(), true, ActivityLogIndexResource::class);

        $data = $result->data;
        $code = $result->code;
        $status = $result->status;
        $message = $result->message;

        return response()->json([
            'data' => ActivityLogIndexResource::collection($data->all()),
            'code' => $code,
            'status' => $status,
            'message' => $message
        ], $code);
    }
}
