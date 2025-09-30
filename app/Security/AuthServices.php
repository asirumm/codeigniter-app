<?php

namespace App\Security;

use App\ApplicationConfiguration\ContainerService;
use App\ApplicationConfiguration\JwtConfig;
use App\ApplicationConfiguration\MonologConfig;
use App\ApplicationConfiguration\SecurityConfiguration;
use Monolog\Logger;

class AuthServices
{
    private AuthProviderInterface $provider;
    private Logger $logger;
    private UserInterface $user;

    public function __construct()
    {
        $this->logger = MonologConfig::getConfig('security',request(),false);
        $this->provider = SecurityConfiguration::$provider;

    }

    /**
     * @param $username
     * @param $password
     * @return void
     * @throws \RuntimeException
     */
    public function authentication($username, $password): void
    {
        // proses authentication null apabila gagal
        $result = $this->provider->authenticate($username,$password);

        if ($result!=null){
            $this->user = new AbstractUser();
            $this->user->setUsername($result[JwtConfig::$credentialUserPayload]);
            $this->user->setRoles((array($result[JwtConfig::$roleUserPayload])));

        }else{
            throw new \RuntimeException("username atau password salah");
        }
    }

    /**
     * Cek otorisasi user berdasarkan roles pada routes parameter dan JWT cookie request.
     *
     * @param array $allowedRoles Daftar role yang diizinkan mengakses resource
     * @param string $jwtCookieRequest JWT cookie dari request
     * @return bool|array True jika akses diperbolehkan (role cocok), false jika gagal,
     *                   atau array user JWT jika akses valid
     * @throws \RuntimeException Jika terjadi kesalahan saat verifikasi JWT
     */
    public function authorization(array $allowedRoles, string $jwtCookieRequest)
    {
        $jwtConfig = new JwtConfig();

        $this->logger->debug("allowed roles param: " . implode(', ', $allowedRoles));

        try {
            // Verifikasi JWT cookie
            $userJwt = $jwtConfig->verifyJWT($jwtCookieRequest);

            // Pastikan payload memiliki key roles dan berupa array
            $userRoles = $userJwt[JwtConfig::$roleUserPayload] ?? null;
            if (!is_array($userRoles)) {
                return false; // JWT tidak memiliki role yang valid
            }

            // Jika tidak ada batasan role (akses publik)
            if (empty($allowedRoles)) {
                return true;
            }

            // Cek apakah ada role user yang cocok dengan allowedRoles
            foreach ($userRoles as $role) {
                if (in_array($role, $allowedRoles, true)) {

                    $this->logger->debug("roles user valid dengan allowedRoles");

                    return $userJwt; // akses valid, return data user JWT
                }
            }

            // Tidak ada role yang cocok
            return false;

        } catch (\Exception $exception) {
            // Tangani error saat verifikasi JWT
            throw new \RuntimeException('JWT verification failed: ' . $exception->getMessage());
        }
    }


    public function getCurrentUser(): UserInterface
    {
        return $this->user;
    }
}