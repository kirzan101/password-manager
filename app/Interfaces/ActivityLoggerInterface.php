<?php

namespace App\Interfaces;

use App\Data\BaseResponse;
use App\Data\StandardResponse;
use App\DTOs\ActivityLoggerDTO;
use Illuminate\Http\Request;

interface ActivityLoggerInterface
{
    /**
     * Add an activity log.
     *
     * @param  Request $request
     * @return StandardResponse
     */
    public function addLog(
        BaseResponse $result,
        Request $request,
        string $module,
        string $type = 'store',
        ?int $processedBy = null,
    ): StandardResponse;
}
