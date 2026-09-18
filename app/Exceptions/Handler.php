<?php

namespace App\Exceptions;

use Throwable;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception): SymfonyResponse
    {
        // Handle validation and authentication errors using Laravel's default
        if (
            $exception instanceof ValidationException ||
            $exception instanceof AuthenticationException
        ) {
            return parent::render($request, $exception);
        }

        // Handle authorization errors (e.g., Gate::authorize)
        if ($exception instanceof AuthorizationException) {
            $status = 403;
            $message = $exception->getMessage() ?: 'You are not authorized to perform this action.';

            return $this->handleResponse($request, $status, $message);
        }

        // Handle other HTTP exceptions or default to 500
        $status = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        $message = $exception->getMessage() ?: SymfonyResponse::$statusTexts[$status] ?? 'An unexpected error occurred.';

        return $this->handleResponse($request, $status, $message);
    }

    /**
     * Shared response builder for both JSON and Inertia requests
     */
    protected function handleResponse(Request $request, int $status, string $message): SymfonyResponse
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => $message], $status);
        }

        return Inertia::render('Error', [
            'code' => $status,
            'message' => $message,
        ])->toResponse($request);
    }

    public function register(): void
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->header('X-Inertia')) {
                return Inertia::render('Error', [
                    'code' => 404,
                    'message' => 'Page Not Found',
                ])->toResponse($request)->setStatusCode(404);
            }
        });
    }
}
