<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndexResource\CredentialIndexResource;
use App\Http\Resources\CredentialResource;
use App\Interfaces\FetchInterfaces\CredentialFetchInterface;
use Illuminate\Http\Request;
use App\Data\ApiResponse;
use App\Interfaces\ManageCredentialInterface;

class CredentialApiController extends Controller
{
    public function __construct(
        private CredentialFetchInterface $credentialFetch,
        private ManageCredentialInterface $credential
    ) {}

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

        $result = $this->credentialFetch->indexCredentials($request->toArray(), true, CredentialResource::class);

        $data = $result->data;
        $code = $result->code;
        $status = $result->status;
        $message = $result->message;

        return new ApiResponse(
            data: [
                'data' => CredentialResource::collection($data->all()),
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
            ],
            code: $code,
        );
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

        $result = $this->credentialFetch->indexCredentials($request->toArray(), true, CredentialIndexResource::class);

        $data = $result->data;
        $code = $result->code;
        $status = $result->status;
        $message = $result->message;

        return new ApiResponse(
            data: [
                'data' => CredentialIndexResource::collection($data->all()),
                'code' => $code,
                'status' => $status,
                'message' => $message
            ],
            code: $code,
        );
    }

    public function reveal(int $credentialId)
    {
        $secret = $this->credential->revealCredentialSecret($credentialId);

        return response()->json([
            'secret' => $secret,
        ]);
    }
}
