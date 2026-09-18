<?php

namespace App\Traits;

use App\Data\BaseResponse;
use App\Helpers\ErrorHelper;
use App\Helpers\Helper;
use Inertia\Inertia;

trait ReturnMessageTrait
{
    /**
     * Returns a standardized response message based on the result of an operation.
     *
     * @param BaseResponse $result
     * @return mixed
     */
    public function returnMessage(?BaseResponse $result = null): mixed
    {
        if (empty($result)) {
            throw new \Exception('An unexpected error occurred.');
        }

        $code = $result->code;
        $status = $result->status;
        $message = $result->message;

        $processedErrorMessage = ErrorHelper::productionErrorMessage($code, $message);

        if ((int) $code >= 500) {
            return Inertia::render('Error', [
                'code' => $code,
                'message' => $processedErrorMessage
            ]);
        }

        return redirect()->back()->with($status, $processedErrorMessage);
    }
}
