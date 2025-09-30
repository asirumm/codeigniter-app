<?php

namespace App\Controllers\View;

use App\ApplicationConfiguration\GoogleOauthConfig;
use App\Controllers\BaseController;

class UserAuthControllerView extends BaseController
{
    public function formLogin()
    {
        $client = GoogleOauthConfig::getConfig();
        $authUrl = $client->createAuthUrl();

        return view('login', ['authUrl' => $authUrl]);
    }
}