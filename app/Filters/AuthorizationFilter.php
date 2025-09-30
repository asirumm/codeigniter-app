<?php

namespace App\Filters;

use App\ApplicationConfiguration\JwtConfig;
use App\ApplicationConfiguration\MonologConfig;
use App\ApplicationConfiguration\SecurityConfiguration;
use App\Security\AuthServices;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class: AuthorizationFilter
 *
 * Filter untuk memvalidasi akses user ke route tertentu
 * yang memerlukan otorisasi (misal: /admin, /dashboard).
 *
 * Catatan:
 * - Gunakan `<?php if (session()->getFlashdata('message')): ?>`
 *   pada view untuk menampilkan pesan error.
 */
class AuthorizationFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        $logger         = MonologConfig::getConfig('security',request(),false);
        $authService    = new AuthServices();
        $jwtConfig      = new JwtConfig();

        // kita convert karena RequestInterface tidak memiliki get data pada post
        if ($request instanceof IncomingRequest){

            // Ambil cookie JWT dari request
            $jwtCookie = $request->getCookie($jwtConfig->cookieName);

            if ($jwtCookie === null) {

                return response()->setJSON([
                    "status"=>"error",
                    "message"=>"anda tidak memiliki akses",
                    "code"=>403
                ],403);
            }



            try {
                // Proses otorisasi
                // return false jika role tidak sesuai, array user JWT jika berhasil
                $authorizationResult = $authService->authorization($arguments, $jwtCookie);

                if ($authorizationResult === false) {
                    return response()->setJSON([
                        "status"=>"error",
                        "message"=>"anda tidak memiliki otorisasi",
                        "code"=>401
                    ],401);
                }

                // Logging akses berhasil
                $logger->info("User '{$authorizationResult[SecurityConfiguration::$credential]}' berhasil mengakses {$request->getUri()}");


            }catch (\Exception $exception) {
                // JWT invalid atau error verifikasi
                $logger->debug("Exception validasi JWT: {$exception->getMessage()}");

                return response()->setJSON([
                    "status"=>"error",
                    "message"=>"anda tidak memiliki akses",
                    "code"=>403
                ],403);
            }


        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // TODO: Implement after() method.
    }
}