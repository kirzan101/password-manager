<?php

namespace App\Helpers;

class ErrorHelper
{
    /**
     * Generate a user-friendly error message based on the error code.
     *
     * @param int $code
     * @param string $defaultMessage
     * @return string
     */
    public static function productionErrorMessage(int $code = 500, string $defaultMessage = 'Something went wrong. Please try again later.'): string
    {
        if (env('APP_ENV') !== 'production') {
            return $defaultMessage;
        }

        // In production, we return a generic error message
        switch ($code) {
            case 400:
                return 'Bad request. Please check your input and try again.';
            case 401:
                return 'Authentication required. Please log in.';
            case 403:
                return 'You do not have permission to view this page.';
            case 404:
                return 'The requested resource was not found.';
            case 405:
                return 'Method not allowed.';
            case 408:
                return 'Request timeout. Please try again.';
            case 422:
                return 'The data provided is invalid.';
            case 429:
                return 'Too many requests. Please slow down.';
            case 500:
                return 'An unexpected error occurred. Please try again later.';
            case 502:
                return 'Bad gateway. Please try again later.';
            case 503:
                return 'Service unavailable. Please try again later.';
            case 504:
                return 'Gateway timeout. Please try again later.';
            default:
                return 'An error occurred. Please try again later.';
        }
    }
}
