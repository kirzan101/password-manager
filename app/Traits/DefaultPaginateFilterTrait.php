<?php

namespace App\Traits;

/**
 * Trait DefaultPaginateFilterTrait
 *
 * Provides a method to set default filters for pagination, including per_page, sort_by, sort, and current_page.
 */
trait DefaultPaginateFilterTrait
{
    /**
     * Set default filters for pagination.
     *
     * @param array $request
     * @param array $allowedSortFields
     * @return array
     */
    public function paginateFilter(array $request, array $allowedSortFields = []): array
    {
        // Validate per_page with a safe default and upper limit
        $perPage = isset($request['per_page']) && is_numeric($request['per_page']) && $request['per_page'] > 0
            ? (int) $request['per_page']
            : 10;
        $perPage = min($perPage, 100);

        // Merge default sortable fields and user-defined ones (without duplicates)
        $allowedSortFields = $this->resolveSortableFields($allowedSortFields);

        // Validate and set sort_by
        $sortBy = isset($request['sort_by']) && in_array($request['sort_by'], $allowedSortFields, true)
            ? $request['sort_by']
            : $allowedSortFields[0];

        // Validate and normalize sort direction
        $sort = strtolower($request['sort'] ?? 'desc');
        $sort = in_array($sort, ['asc', 'desc']) ? $sort : 'desc';

        // commented out the current_page logic to prioritize 'page' parameter if provided
        // $currentPage = isset($request['current_page']) && is_numeric($request['current_page']) && $request['current_page'] > 0
        //     ? (int) $request['current_page']
        //     : 1;

        // If 'page' is provided in the request, it takes precedence over 'current_page'
        $currentPage = isset($request['page']) && is_numeric($request['page']) && $request['page'] > 0
            ? (int) $request['page']
            : 1;

        return [
            'per_page' => $perPage,
            'sort_by' => $sortBy,
            'sort' => $sort,
            'current_page' => $currentPage
        ];
    }

    /**
     * Merge default sortable fields with user-defined ones.
     *
     * @param array $customFields
     * @return array
     */
    private function resolveSortableFields(array $customFields): array
    {
        $defaultFields = ['id', 'created_at', 'updated_at'];
        return array_unique(array_merge($defaultFields, $customFields));
    }
}
