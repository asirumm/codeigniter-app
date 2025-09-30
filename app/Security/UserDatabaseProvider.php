<?php

namespace App\Security;

use App\ApplicationConfiguration\ContainerService;
use App\ApplicationConfiguration\JwtConfig;
use App\ApplicationConfiguration\MonologConfig;
use App\Models\UserModel;

class UserDatabaseProvider implements AuthProviderInterface
{

    public function __construct()
    {
    }

    /**
     * @param string $username
     * @param string $password
     * @return array|null ketika user tidak ditemukan akan null, dan array username, password ketika user ditemukan
     */
    public function authenticate(string $username, string $password): ?array
    {
//        $logger = MonologConfig::getConfig('security',request());
//
//        // Hey sesuaikan ini ketika anda memiliki identifikasi login berbeda
//        $result = $this->userModel->where(["email"=>$username])->first();
//
//        if ($result!=null){
//            $logger->debug("user dengan username {$username} role {$result['role']} terdaftar di database");
//
//            return [JwtConfig::$credentialUserPayload => $result['email'], JwtConfig::$roleUserPayload => $result['role']];
//        }
//
//        $logger->debug("user dengan username {$username} tidak terdaftar pada database");
//
        return  null;
    }
}