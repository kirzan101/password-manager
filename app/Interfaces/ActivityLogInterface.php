<?php

namespace App\Interfaces;

use App\Data\ModelResponse;
use App\DTOs\ActivityLogDTO;

interface ActivityLogInterface
{
    /**
     * Store a new activity log in the database.
     *
     * @param ActivityLogDTO $activityLogDTO
     * @return ModelResponse
     */
    public function storeActivityLog(ActivityLogDTO $activityLogDTO): ModelResponse;

    /**
     * update an existing activity log in the database.
     *
     * @param ActivityLogDTO $activityLogDTO
     * @param integer $activityLogId
     * @return ModelResponse
     */
    public function updateActivityLog(ActivityLogDTO $activityLogDTO, int $activityLogId): ModelResponse;

    /**
     * delete a activity log from the database.
     *
     * @param integer $activityLogId
     * @return ModelResponse
     */
    public function deleteActivityLog(int $activityLogId): ModelResponse;
}
