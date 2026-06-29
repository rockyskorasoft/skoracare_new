<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

trait WebResponseTrait
{
    /**
     * function to return the success response
     * @param mixed $routeName
     * @param mixed $message
     * @param array $additionalData
     * @return \Illuminate\Http\RedirectResponse
     */
    public function successResponse($routeName, $message = "", array $additionalData = []): RedirectResponse
    {
        return redirect()->route($routeName, $additionalData)->with('message', $message);
    }

    /**
     * function to return the error response
     * @param \Exception $exception
     * @return RedirectResponse
     */
    public function errorResponse(Exception $exception)
    {
        return redirect()->back()->with('error', $exception->getMessage())->withInput();
    }


    /**
     * function to handle the ajax success response
     *
     * @param string $message
     * @param array $data
     * @param Response $code
     * @return mixed
     */
    public function successAjaxResponse(string $message, mixed $data = [], $code = Response::HTTP_OK)
    {
        $response = [];
        $response['status'] = 'success';
        if ($message) {
            $response['message'] = $message;
        }
        if ($data) {
            $response['data'] = $data;
        }
        return response()->json($response);
    }

    /**
     * function to handle the ajax error response
     *
     * @param mixed $exception
     * @param Response $code
     * @param array $request
     * @return mixed
     */
    public function errorAjaxResponse($exception, $code = Response::HTTP_BAD_REQUEST, array $request = [])
    {
        if ($exception instanceof Throwable || $exception instanceof Exception) {
            if ($exception->getMessage() == "The payload is invalid.") {
                $error = trans('auth.invalid');
            } else {
                $error = trans('app.internal_server_error');
            }
        } else {
            $error = $exception;
        }
        return response()->json(
            [
                'status' => 'error',
                'message' => $error
            ],
            $code
        );
    }

    /**
     * function to handle the general error response
     *
     * @param mixed $exception
     * @param Response $code
     * @return mixed
     */
    public function handleErrorResponse($exception, $code = Response::HTTP_BAD_REQUEST)
    {
        if ($exception instanceof Throwable || $exception instanceof Exception) {

            if ($exception->getMessage() == "The payload is invalid.") {
                $error = trans('auth.invalid');
            } else {
                $error = $exception->getMessage() ?: trans('app.internal_server_error');
            }
        } else {
            $error = $exception ?: trans('app.internal_server_error');
        }

        return response()->json([
            'status' => 'error',
            'message' => $error,
        ], $code);
    }
}
