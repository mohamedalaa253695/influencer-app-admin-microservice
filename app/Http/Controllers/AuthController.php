<?php
namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use InfluencerMicroservices\UserService;

// use influencerMicroservices\UserService;

class AuthController
{
    public function user()
    {
        return  new UserResource((new UserService)->getUser());
    }
}
