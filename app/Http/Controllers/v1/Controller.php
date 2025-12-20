<?php

namespace App\Http\Controllers\v1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

abstract class Controller
{
    protected $MSG_LOGIN_FAILED    = 'Login failed. Please try again.';
    protected $MSG_LOGIN_SUCCESS   = 'Login successful.';

    protected $MSG_REGISTER_FAILED  = 'Registration failed. Please check your data.';
    protected $MSG_REGISTER_SUCCESS = 'Account created successfully.';

    protected $MSG_LOGOUT_FAILED   = 'Unable to logout. Session may have already expired.';
    protected $MSG_LOGOUT_SUCCESS  = 'Logged out successfully.';

    protected function validateRequest($request, $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->respondWithError($validator->errors()->first());
        }
    }


    protected function respondWithToken($token, $message)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
            'message' => $message
        ]);
    }

    protected function respondWithSuccess($data, $message)
    {
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'message' => $message
        ]);
    }

    protected function respondWithError($message)
    {
        return response()->json([
            'status' => 'error',
            'data' => null,
            'message' => $message
        ]);
    }
}
