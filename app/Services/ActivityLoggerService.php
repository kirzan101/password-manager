<?php

namespace App\Services;

use App\Data\StandardResponse;
use App\Data\BaseResponse;
use App\DTOs\ActivityLogDTO;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Interfaces\ActivityLoggerInterface;
use App\Interfaces\ActivityLogInterface;
use App\Traits\HttpErrorCodeTrait;
use App\Traits\ReturnModelCollectionTrait;
use App\Traits\ReturnModelTrait;
use App\Traits\CheckIfColumnExistsTrait;
use App\Traits\DetectsSoftDeletesTrait;
use App\Traits\EnsureDataTrait;
use App\Traits\EnsureSuccessTrait;
use Illuminate\Support\Facades\DB;

class ActivityLoggerService implements ActivityLoggerInterface
{
    use HttpErrorCodeTrait,
        ReturnModelCollectionTrait,
        ReturnModelTrait,
        DetectsSoftDeletesTrait,
        CheckIfColumnExistsTrait,
        EnsureSuccessTrait,
        EnsureDataTrait;

    public function __construct(
        private ActivityLogInterface $activityLog
    ) {}

    /**
     * Fields to exclude from logging
     * 
     * Note: removed the older 'old_properties' from the excluded fields as it is now being handled separately
     */
    const EXCLUDED_FIELDS = [
        '_token',
        '_method',
        'password',
        'password_confirmation',
        'forceFormData',
        'old_properties',
        'current_password',
        'new_password',
        'confirm_password',
    ];

    /**
     * Add an activity log entry.
     *
     * @param BaseResponse $result
     * @param Request $request
     * @param string $module
     * @param string $type
     * @param int|null $processedBy
     * @return StandardResponse
     */
    public function addLog(
        BaseResponse $result,
        Request $request,
        string $module,
        string $type = 'store',
        ?int $processedBy = null,
    ): StandardResponse {
        try {
            $status = $result->status;
            $message = $result->message;
            $old_properties = $request->input('old_properties');

            if (!is_null($old_properties) && is_array($old_properties)) {
                // remove _token, _method, passwords & password_confirmation in old_properties if they exist
                $old_properties = \Illuminate\Support\Arr::except(
                    $old_properties,
                    self::EXCLUDED_FIELDS
                );
            }

            $activityLogDTO = ActivityLogDTO::fromArray([
                'module' => $module,
                'description' => $message,
                'status' => $status,
                'type' => $type,
                'properties' => $request->except(self::EXCLUDED_FIELDS),
                'old_properties' => $old_properties,
                'processed_by' => $processedBy,
            ]);

            if ($processedBy !== null) {
                $activityLogDTO = $activityLogDTO->withProcessedBy($processedBy);
            }

            $activityLogResult = $this->activityLog->storeActivityLog($activityLogDTO);
            $this->ensureSuccess($activityLogResult->toArray(), 'Failed to store activity log.');

            return StandardResponse::success($activityLogResult->code, $activityLogResult->status, $activityLogResult->message);
        } catch (\Throwable $th) {
            $code = $this->httpCode($th);
            return StandardResponse::error($code, Helper::ERROR, $th->getMessage());
        }
    }
}
