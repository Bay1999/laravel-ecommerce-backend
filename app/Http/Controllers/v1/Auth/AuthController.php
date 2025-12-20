<?php

namespace App\Http\Controllers\v1\Auth;

use App\Http\Controllers\v1\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  protected $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }


  // REGISTER: Create a new user
  public function register(Request $request)
  {
    $this->validateRequest($request, [
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:8|confirmed',
    ]);

    $user = $this->userService->create($request->all());

    return response()->json(['message' => $this->MSG_REGISTER_SUCCESS], 201);
  }

  // LOGIN: Verify user and return token
  public function login(Request $request)
  {
    $credentials = $request->only('email', 'password');

    if (!$token = Auth::guard('api')->attempt($credentials)) {
      return $this->respondWithError($this->MSG_LOGIN_FAILED);
    }

    return $this->respondWithToken($token, $this->MSG_LOGIN_SUCCESS);
  }

  public function logout(Request $request)
  {
    Auth::guard('api')->logout();

    return response()->json([
      'message' => $this->MSG_LOGOUT_SUCCESS
    ]);
  }
}
