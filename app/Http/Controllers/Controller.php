<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Return a success response with data
     */
    protected function success($message = null, $data = null, $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Return an error response
     */
    protected function error($message = null, $data = null, $code = 400)
    {
        $response = [
            'success' => false,
            'message' => $message ?? 'An error occurred',
        ];

        if ($data !== null) {
            $response['errors'] = $data;
        }

        return response()->json($response, $code);
    }
}
