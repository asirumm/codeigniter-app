<?php

namespace App\Controllers\Api;

class UserAuthController extends BaseApiController
{
    public function login()
    {
        $username = $this->request->getJsonVar("username");
        return $this->responseSuccess(["username"=>$username],"success login");
    }
}