<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserService
{
  protected $user;

  public function __construct(UserRepository $user)
  {
    $this->user = $user;
  }

  public function getAll()
  {
    return $this->user->all();
  }

  public function create($data)
  {
    return $this->user->create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
    ]);
  }
}
