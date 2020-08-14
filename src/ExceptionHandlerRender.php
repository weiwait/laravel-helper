<?php

namespace Weiwait\Helper;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Router;
use Illuminate\Validation\ValidationException;
use Throwable;

trait ExceptionHandlerRender
{
    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Throwable $exception
     * @return JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        if (method_exists($exception, 'render') && $response = $exception->render($request)) {
            return Router::toResponse($request, $response);
        } elseif ($exception instanceof Responsable) {
            return $exception->toResponse($request);
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->modelNotFound($request, $exception);
        }

        $exception = parent::prepareException($exception);

        if ($exception instanceof HttpResponseException) {
            return $exception->getResponse();
        } elseif ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        } elseif ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $request->expectsJson()
            ? $this->prepareJsonResponse($request, $exception)
            : parent::prepareResponse($request, $exception);
    }

    protected function prepareJsonResponse($request, Throwable $e)
    {
        return new JsonResponse([
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'status' => $this->isHttpException($e) ? $e->getStatusCode() : 500,
        ],
            200,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'message' => '存在不正确的参数',
            'errors' => $exception->errors(),
            'code' => $exception->getCode(),
            'status' => 422,
        ], 200);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
            ? response()->json([
                'message' => '尚未认证',
                'code' => $exception->getCode(),
                'status' => 401,
            ], 200)
            : redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    protected function modelNotFound(\Illuminate\Http\Request $request, Throwable $exception)
    {
        return response()->json([
            'message' => '查找的资源不存在',
            'errors' => $exception->errors(),
            'code' => $exception->getCode(),
            'status' => 404,
        ], 200);
    }
}
