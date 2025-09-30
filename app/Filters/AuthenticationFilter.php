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
use Monolog\Logger;

/**
 * Class: AuthenticationFilter
 *
 * Filter khusus untuk proses login.
 *
 * Fungsi utama:
 * 1. Memproses username dan password dari request.
 * 2. Mengautentikasi user melalui AuthServices.
 * 3. Membuat JWT cookie sesuai konfigurasi JwtConfig.
 * 4. Menangani kegagalan autentikasi dan redirect ke login form.
 * 
 * Note :
 * Gunakan  `<?php if (session()->getFlashdata('message')): ?>`
 * pada view untuk menampilkan error
 */
class AuthenticationFilter implements FilterInterface
{
    /** @var string|null JWT token untuk diset sebagai cookie */
    private ?string $jwtToken = null;

    /** @var JwtConfig Konfigurasi JWT */
    private JwtConfig $jwtConfig;

    /** @var Logger Logger untuk mencatat aktivitas login */
    private Logger $logger;

    public function __construct()
    {
        $this->jwtConfig = new JwtConfig();
        $this->logger    = MonologConfig::getSecurityLogger();
    }

    /**
     * Sebelum request diproses controller
     *
     * @param RequestInterface $request
     * @param mixed $arguments
     * @return mixed|null Redirect jika gagal autentikasi
     */
    public function before(RequestInterface $request, $arguments = null)
    {

        $throttler = service('throttler');

        // Restrict an IP address to no more than 1 request
        // per second across the entire site.
        if ($throttler->check(md5($request->getIPAddress()), 60, MINUTE) === false) {
            return response()->setJSON([
                "status"=>"error",
                "message"=>"terlalu banyak request",
                "code"=>429
            ],429);
        }

        // kita convert karena RequestInterface tidak memiliki get data pada post
        if ($request instanceof IncomingRequest) {
            // Ambil credential dari request
            $username = $request->getJsonVar(SecurityConfiguration::$credential);
            $password = $request->getJsonVar(SecurityConfiguration::$password);


            if ($username===null || $password===null){
                $this->logger->info("salah");

                return response()->setJSON([
                    "status"  => "error",
                    "message" => "api request tidak sesuai",
                    "code"    => 400
                ],400);
            }

            $authService = new AuthServices();


            try {
                $this->logger->debug("user login request username : {$username} password : {$password}");

                // Proses autentikasi
                $authService->authentication($username, $password);

                // Ambil data user yang login
                $currentUser = $authService->getCurrentUser();

                // Setup payload untuk JWT
                $jwtPayload = [
                    JwtConfig::$credentialUserPayload => $currentUser->getIdentifier(),
                    JwtConfig::$roleUserPayload => $currentUser->getRoles()
                ];

                // Generate JWT token
                $this->jwtToken = $this->jwtConfig->generate($jwtPayload);


                // ketika gagal username atau password pada proses authentication
            } catch (\Exception $exception) {

                return response()->setJSON([
                    "status"=>"error",
                    "message"=>$exception->getMessage(),
                    "code"=>400
                ])->setStatusCode(400);
            }
        }
    }

    /**
     * Setelah request diproses controller
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param mixed $arguments
     * @return ResponseInterface
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Set cookie JWT jika ada
        if ($this->jwtToken !== null) {
            $response->setCookie(
                $this->jwtConfig->cookieName,
                $this->jwtToken
            );
        }

        return $response;
    }
}
