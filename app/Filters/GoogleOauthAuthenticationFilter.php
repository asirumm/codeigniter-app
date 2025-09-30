<?php

namespace App\Filters;

use App\ApplicationConfiguration\ContainerService;
use App\ApplicationConfiguration\GoogleOauthConfig;
use App\ApplicationConfiguration\JwtConfig;
use App\ApplicationConfiguration\MonologConfig;
use App\ApplicationConfiguration\SecurityConfiguration;
use App\Security\AuthServices;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Google_Service_Oauth2;
use Monolog\Logger;
use function PHPUnit\Framework\isEmpty;

/**
 * Class: GoogleOauthAuthenticationFilter
 *
 * Filter untuk login user menggunakan OAuth Google (Gmail).
 *
 * Mengapa dibuat:
 * - Memudahkan user login tanpa password, cukup dengan akun Gmail mereka.
 * - Menyimpan email user di database untuk validasi apakah user terdaftar.
 * - Memberikan JWT cookie setelah callback dari Google.
 *
 * Proses:
 * 1. User klik login dengan Google
 * 2. Google mengirim callback dengan authorization code
 * 3. Verifikasi user apakah terdaftar / memiliki hak akses
 * 4. Jika valid, generate JWT dan set cookie
 */
class GoogleOauthAuthenticationFilter implements FilterInterface
{
    /** @var string|null JWT cookie yang akan diset ke user */
    private ?string $jwtCookie = null;
    private JwtConfig $jwtConfig;
    private Logger $logger;

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig();
        $this->logger    = MonologConfig::getSecurityLogger();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $googleClient = GoogleOauthConfig::getConfig();

        // kita convert karena RequestInterface tidak memiliki get data pada post
        if ($request instanceof IncomingRequest) {

            // code berasal dari google
            $authCode = $request->getVar('code');
            if (!$authCode) {

                $this->logger->warning("parameter 'code' tidak ditemukan");

                // TODO untuk api bagaimana? sekarang masih form
                return redirect(SecurityConfiguration::$routeLoginFormName)
                    ->with("message", "Terjadi kesalahan");
            }


            // code berasal dari google
            $googleResponseCallback = $request->getVar('code');


            if (isEmpty($googleResponseCallback)) {
                // mendapatkan data refresh token dan sebagainya dari google
                // Ambil token
                $token = $googleClient->fetchAccessTokenWithAuthCode($googleResponseCallback);


                $this->logger->debug(json_encode($token));

                // Kalau ada error di token, log dulu
                if (isset($token['error'])) {

                    $this->logger->error("gagal ambil token: " . json_encode($token));

                    return redirect(SecurityConfiguration::$routeLoginFormName)
                        ->with("message", "Gagal autentikasi Google");
                }

                try {
                    // Set token ke client
                    $googleClient->setAccessToken($token);

                    // Ambil info user dari Google
                    $oauth2      = new Google_Service_Oauth2($googleClient);
                    $userInfo    = $oauth2->userinfo->get();
                    $userEmail   = $userInfo->email;


                    // Autentikasi user berdasarkan email (password kosong)
                    // password kosong karena kita hanya akan cek nama
                    // email apakah terdaftar dalam database kita
                    $authService = new AuthServices();
                    $authService->authentication($userEmail, "");


                    $user = $authService->getCurrentUser();
                    $this->logger->debug("berhasil masuk user {$userEmail}");

                    // Setup payload JWT
                    $payload = [
                        JwtConfig::$credentialUserPayload => $user->getIdentifier(),
                        JwtConfig::$roleUserPayload => $user->getRoles()
                    ];

                    // Generate JWT cookie
                    $this->jwtCookie = $this->jwtConfig->generate($payload);

                } catch (\Exception $exception) {
                    $this->logger->debug("user tidak terdaftar: {$exception->getMessage()}");

                    return redirect(SecurityConfiguration::$routeLoginFormName)
                        ->with("message", $exception->getMessage());
                }
            }}

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // set cookie
        if ($this->jwtCookie !== null) {
            $response->setCookie(
                $this->jwtConfig->cookieName,
                $this->jwtCookie
            );
        }

        return $response;
    }
}