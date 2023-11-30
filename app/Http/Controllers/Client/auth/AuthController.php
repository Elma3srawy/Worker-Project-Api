<?php

namespace App\Http\Controllers\Client\auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\loginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\Client;
use App\Http\Requests\Auth\AuthClientRequest;
use App\Services\Auth\AuthService;

class AuthController extends Controller
{
  use ApiResponses;
  public function register(AuthClientRequest $request) {

    try {
        $user = Client::create([
          "name" => $request->name,
          "email" => $request->email,
          "password" => $request->password,
        ]);

        event(new Registered($user));
        $token = auth("client")->login($user);

        return $this->respondWithToken($token,"client",'Registration successful. Please check your email for account activation');

      }
      catch (JWTException $e) {
        return $this->errorResponse($e->getMessage());
      }
  }
  public function login(loginRequest $request) {
    try {

        $credentials = ["email" => $request->email , "password" =>$request->password];

        if (!$token = auth("client")->attempt($credentials)) {
          return $this->errorResponse("Unauthenticate");
        }
          return  $this->respondWithToken($token,"client","Login successful");
      }
      catch (JWTException $e) {
        return  $this->errorResponse($e->getMessage());
      }
  }

    public function profile() {
      return (new AuthService)->profile("client");
    }
    public function logout() {
      return (new AuthService)->logout("client");
    }
    public function refresh() {
      return (new AuthService)->refresh("client");
    }

}
