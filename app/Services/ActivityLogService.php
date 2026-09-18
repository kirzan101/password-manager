<?php

namespace App\Services;

use App\DTOs\ActivityLogDTO;
use App\Helpers\Helper;
use App\Interfaces\ActivityLogInterface;
use App\Models\ActivityLog;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelTrait;
use App\Interfaces\CurrentUserInterface;
use App\Interfaces\BaseInterface;
use App\Interfaces\FetchInterfaces\BaseFetchInterface;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use Illuminate\Support\Facades\DB;
use App\Data\ModelResponse;

class ActivityLogService implements ActivityLogInterface
{
    use HttpErrorCodeTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait;

    public function __construct(
        private BaseInterface $base,
        private BaseFetchInterface $fetch,
        private CurrentUserInterface $currentUser
    ) {}

    /**
     * Store a new activity log in the database.
     * @param ActivityLogDTO $activityLogDTO
     * @return ModelResponse
     * @throws \Throwable
     */
    public function storeActivityLog(ActivityLogDTO $activityLogDTO): ModelResponse
    {
        try {
            return DB::transaction(function () use ($activityLogDTO) {
                $currentProfileId = ($activityLogDTO->processed_by ?? $this->currentUser->getProfileId());

                $activityLogData = $activityLogDTO->withProcessedBy($currentProfileId)->toArray();
                $activityLog = $this->base->store(ActivityLog::class, $activityLogData);

                return ModelResponse::success(201, Helper::SUCCESS, 'Activity log created successfully!', $activityLog, $activityLog->id);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Update an existing activity log in the database.
     * @param ActivityLogDTO $activityLogDTO
     * @param int $activityLogId
     * @return ModelResponse
     * @throws \Throwable
     */
    public function updateActivityLog(ActivityLogDTO $activityLogDTO, int $activityLogId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($activityLogDTO, $activityLogId) {

                $activityLog = $this->fetch->showQuery(ActivityLog::class, $activityLogId)->firstOrFail();

                $currentProfileId = ($activityLogDTO->processed_by ?? $this->currentUser->getProfileId());
                $activityLogData = ActivityLogDTO::fromModel($activityLog, $activityLogDTO->toArray())
                    ->withProcessedBy($currentProfileId)
                    ->toArray();

                $activityLog = $this->base->update($activityLog, $activityLogData);

                return ModelResponse::success(200, Helper::SUCCESS, 'Activity log updated successfully!', $activityLog, $activityLogId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }

    /**
     * Delete an existing activity log from the database.
     * @param int $activityLogId
     * @return ModelResponse
     * @throws \Throwable
     */
    public function deleteActivityLog(int $activityLogId): ModelResponse
    {
        try {
            return DB::transaction(function () use ($activityLogId) {
                $activityLog = $this->fetch->showQuery(ActivityLog::class, $activityLogId)->firstOrFail();

                $this->base->delete($activityLog);

                return ModelResponse::success(204, Helper::SUCCESS, 'Activity log deleted successfully!', null, $activityLogId);
            });
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return ModelResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
