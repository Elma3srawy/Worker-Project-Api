<?php

namespace App\Http\Controllers\Client\auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Auth\Events\Verified;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    use ApiResponses;
    public function __invoke(EmailVerificationRequest $request)
    {

            if ($request->user("client")->hasVerifiedEmail()) {
                return $this->errorResponse("Your email is already verified");
            }

            if ($request->user("client")->markEmailAsVerified()) {
                event(new Verified($request->user()));
            }


            return $this->okResponse("Email Verified Successfully");

    }







}
