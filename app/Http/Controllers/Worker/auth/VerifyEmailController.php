<?php

namespace App\Http\Controllers\Worker\auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\AuthService;
use App\Traits\ApiResponses;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    use ApiResponses;
    public function __invoke(EmailVerificationRequest $request)
    {

        if ($request->user("worker")->hasVerifiedEmail()) {
            return $this->errorResponse("Your email is already verified");
        }

        if ($request->user("worker")->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }


        return $this->okResponse("Email Verified Successfully");
    }
}
