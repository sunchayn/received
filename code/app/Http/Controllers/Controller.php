<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Redirect to the specified route with the given errors while taking care of ajax requests.
     *
     * @param $route
     * @param $error
     * @param int $status
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function redirectWithError($route, $error, $status = 422)
    {
        if (request()->ajax()) {
            return response()->json(['error' => $error], $status);
        } else {
            return redirect()
                ->route($route)
                ->withErrors(['error' => $error])
                ->withInput()
            ;
        }
    }

    /**
     * Return a JSON error
     *
     * @param $error
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonError($error, $status = 422)
    {
        return response()->json([
            'error' => $error,
        ], $status);
    }

    /**
     * Return JSON success message
     *
     * @param $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonSuccess($message, $status = 200)
    {
        return response()->json([
            'success' => $message,
        ], $status);
    }

    /**
     * return JSON error with HTTP Unprocessable Entity Code.
     *
     * @param $error
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonUnprocessableEntity($error)
    {
        return response()->json([
            'error' => $error,
        ], 422);
    }

    /**
     * Return a json array of validation errors with HTTP Unprocessable Entity Code.
     *
     * @param $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function validationErrors($errors)
    {
        return response()->json([
            'errors' => $errors,
        ], 422);
    }
}
