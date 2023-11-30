<?php
namespace App\Services\Auth;
use App\Traits\ApiResponses;

class AuthService{

  use ApiResponses;

public function logout(string $guard) {
  auth($guard)->logout();
  return $this->okResponse("LogOut Successfully!");
}
public function refresh(string $guard) {
    $token = auth($guard)->refresh();
    return $this->respondWithToken($token,$guard);
}
public function profile(string $guard) {
    $user = auth($guard)->user();
    return $this->successResponse($user);
}

}

?>
