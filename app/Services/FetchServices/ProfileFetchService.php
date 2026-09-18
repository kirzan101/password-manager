<?php

namespace App\Services\FetchServices;

use App\Data\CollectionResponse;
use App\Data\ModelResponse;
use App\Data\PaginateResponse;
use App\Helpers\Helper;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Interfaces\FetchInterfaces\ProfileFetchInterface;
use App\Models\Profile;
use App\Traits\DefaultPaginateFilterTrait;
use App\Traits\HttpErrorCodeTrait;
use Illuminate\Pagination\Paginator;

class ProfileFetchService implements ProfileFetchInterface
{
    use HttpErrorCodeTrait,
        DefaultPaginateFilterTrait;

    public function __construct(private BaseFetchInterface $fetch) {}

    /**
     * Fetch a list of profiles with optional search functionality.
     *
     * @param array $request
     * @param bool $isPaginated
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return PaginateResponse|CollectionResponse
     */
    public function indexProfiles(array $request = [], bool $isPaginated = false, ?string $resourceClass = null): PaginateResponse|CollectionResponse
    {
        try {
            $query = $this->fetch->indexQuery(Profile::class);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            if (!empty($request['search'])) {
                $search = $request['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('nickname', 'like', "%{$search}%");
                });
            }

            if ($isPaginated) {
                $allowedFields = (new Profile())->getFillable();

                [
                    'per_page' => $per_page,
                    'sort_by' => $sort_by,
                    'sort' => $sort,
                    'current_page' => $current_page
                ] = $this->paginateFilter($request, $allowedFields);

                // Manually set the current page
                Paginator::currentPageResolver(fn() => $current_page ?? 1);

                $profiles = $query->orderBy($sort_by, $sort)->paginate($per_page);
                return PaginateResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $profiles);
            } else {

                $profiles = $query->get();
                return CollectionResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $profiles);
            }
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return CollectionResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Fetch a single Profile by ID.
     *
     * @param int $profileId
     * @param class-string<\Illuminate\Http\Resources\Json\JsonResource>|null $resourceClass
     * @return ModelResponse
     */
    public function showProfile(int $profileId, ?string $resourceClass = null): ModelResponse
    {
        try {
            $query = $this->fetch->showQuery(Profile::class, $profileId);

            if ($resourceClass !== null && isset($resourceClass::$relations)) {
                $query->with($resourceClass::$relations ?? []);
            }

            $profile = $query->firstOrFail();

            return ModelResponse::success(200, Helper::SUCCESS, 'Successfully fetched!', $profile, $profileId);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);

            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
