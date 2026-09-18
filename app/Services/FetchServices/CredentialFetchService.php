<?php

namespace App\Services\FetchServices;

use App\Data\PaginateResponse;
use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Helpers\Helper;
use App\Models\Credential;
use App\Interfaces\FetchInterfaces\CredentialFetchInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\DefaultPaginateFilterTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class CredentialFetchService extends BaseFetchService implements CredentialFetchInterface
{
    use HttpErrorCodeTrait,
        DefaultPaginateFilterTrait;

    /**
     * Fetch a list of credential with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return PaginateResponse|CollectionResponse A response containing the list of credentials, either paginated or as a collection.
     */
    public function indexCredentials(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse
    {
        try {
            $query = $this->indexQuery(Credential::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            // Category filter
            if (isset($request['category']) && !empty($request['category'])) {
                $query->where('category', $request['category']);
            }

            //Search filter
            if (isset($request['search']) && !empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new Credential())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $credentials = $query->orderBy($sort_by, $sort)->paginate($per_page);
                return PaginateResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $credentials);
            } else {
                $credentials = $query->get();
                return CollectionResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $credentials);
            }
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single credential by ID.
     *
     * @param integer $credentialId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse
     */
    public function showCredential(int $credentialId, ?string $resourceClass = null): ModelResponse
    {
        try {
            $query = $this->showQuery(Credential::class, $credentialId);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $model = $query->firstOrFail();

            return ModelResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $model, $credentialId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
