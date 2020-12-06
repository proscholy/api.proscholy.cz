<?php

namespace App\Firebase;

// use Firebase\Auth\Token\Verifier;
use App\PublicModels\PublicUser;

class Guard
{
    // protected $verifier;
    public function __construct()
    {
        // $this->verifier = $verifier;
    }

    public function public_user($request)
    {
        return PublicUser::first();

        // $jwt = $request->bearerToken();

        // $cached = PublicUser::whereLastJwtToken($jwt)->first();

        // if (isset($jwt) && $jwt && $cached) {
        //     return $cached;
        // }

        // // this takes quite a long time (cca 5s)
        // $token = $this->verifier->verifyIdToken($jwt);

        // return PublicUser::getPublicUserFromClaims($token->getClaims(), $jwt);
    }
}
