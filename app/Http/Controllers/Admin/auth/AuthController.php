<?php

namespace App\Http\Controllers\Admin\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticationRequest;
use App\Http\Requests\Auth\loginRequest;
use App\Traits\ApiResponses;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Admin;
use App\Services\Auth\AuthService;


class AuthController extends Controller
{
  use ApiResponses;
  public function register(AuthenticationRequest $request) {

    try {
      $user = Admin::create([
          "name" => $request->name,
          "email" => $request->email,
          "password" => $request->password,
        ]);

        $token = auth()->login($user);


        return  $this->respondWithToken($token,"admin" , 'Registration successful');

      }
      catch (JWTException $e) {
        return $this->errorResponse($e->getMessage());
      }
  }
  public function login(loginRequest $request) {
    try {

        $credentials = ["email" => $request->email , "password" =>$request->password];

        if (!$token = auth()->attempt($credentials)) {
          return $this->errorResponse("Unauthenticate");
        }
          return  $this->respondWithToken($token,"admin" ,"Login successful");
      }
      catch (\Exception $e) {
        return  $this->errorResponse($e->getMessage());
      }
  }

    public function profile() {
      return (new AuthService)->profile("admin");
    }
    public function logout() {
      return (new AuthService)->logout("admin");
    }
    public function refresh() {
      return (new AuthService)->refresh("admin");
    }




}
