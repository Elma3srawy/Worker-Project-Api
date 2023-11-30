<?php

namespace App\Http\Controllers\Worker\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthWorkerRequest;
use App\Models\Worker;
use App\Traits\ApiResponses;
use App\Services\Auth\AuthService;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Auth\loginRequest;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
  use ApiResponses;
  public function register(AuthWorkerRequest $request) {

    try {
        $user = Worker::create([
          "name" => $request->name,
          "email" => $request->email,
          "password" => $request->password,
          "phone" => $request->phone,
          "location" => $request->location,
        ]);

        event(new Registered($user));

        $token = auth("worker")->login($user);
        return $this->respondWithToken($token,'worker', 'Registration successful. Please check your email for account activation');


      }
      catch (JWTException $e) {
        return $this->errorResponse($e->getMessage());
      }
  }
  public function login(loginRequest $request) {
    try {

        $credentials = ["email" => $request->email , "password" =>$request->password];

        if (!$token = auth("worker")->attempt($credentials)) {
          return $this->errorResponse("Unauthenticate");
        }
          return  $this->respondWithToken($token,"worker","Login successful");
      }
      catch (JWTException $e) {
        return  $this->errorResponse($e->getMessage());
      }
  }

    public function profile() {
      return (new AuthService)->profile("worker");
    }
    public function logout() {
      return (new AuthService)->logout("worker");
    }
    public function refresh() {
      return (new AuthService)->refresh("worker");
    }

}
