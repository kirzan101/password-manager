<?php

namespace App\Traits;

use App\Data\BaseResponse;
use App\DTOs\ActivityLogDTO;
use Illuminate\Http\Request;
use LogicException;

trait ActivityLoggerTrait
{
    /**
     * This trait is NOT USED! USED THE ActivityLogService instead.
     */
    public function logActivity(
        BaseResponse $result,
        Request $request,
        string $module,
        string $type = 'store',
        ?int $processedBy = null,
    ): void {
        if (!property_exists($this, 'activityLog')) {
            throw new LogicException('Class using ActivityLoggerTrait must define $activityLog property.');
        }

        $status = $result->status;
        $message = $result->message;

        $activityLogDTO = ActivityLogDTO::fromArray([
            'module' => $module,
            'description' => $message,
            'status' => $status,
            'type' => $type,
            'properties' => $request->except(['_token', '_method']),
        ]);

        if ($processedBy !== null) {
            $activityLogDTO = $activityLogDTO->withDefaultAudit($processedBy);
        }

        $this->activityLog->storeActivityLog($activityLogDTO);
    }
}
