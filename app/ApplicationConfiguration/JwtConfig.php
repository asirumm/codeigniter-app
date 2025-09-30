<?php

namespace App\ApplicationConfiguration;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;

final class JwtConfig
{
    private string $KEY;
    private string $time;
    public string $algorithm = 'HS256';
    public string $cookieName = 'AUTH-COOKIE';
    /**
     * Deklarasi key payload JWT.
     *
     * Fungsi utama:
     * - Menentukan key yang akan digunakan dalam payload JWT.
     * - Memudahkan semua bagian aplikasi untuk mengetahui data user apa saja
     *   yang disimpan di cookie JWT.
     *
     * Digunakan di:
     * - AuthenticationFilter: membuat cookie JWT setelah login
     * - UserServices / Authorization: membaca role dan username dari JWT
     */
    public static string $credentialUserPayload = "username"; // key untuk username user
    public static string $roleUserPayload = "roles";           // key untuk daftar role user


    public function __construct()
    {
        $this->KEY  = getenv('encryption.key');
        $this->time = 4 * 3600; // 4 jam

    }

    public function generate(array $payload): string
    {
        $now = time();

        $payload = array_merge($payload, [
            'iat' => $now,
            'exp' => $now + $this->time,
        ]);

        return JWT::encode($payload, $this->KEY, $this->algorithm);
    }

    /**
     * @param $token
     * @return array
     * @throws \RuntimeException
     */
    public function verifyJWT($token): array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->KEY, 'HS256'));

            return (array) $decoded;

        } catch (Exception $e) {

            throw new RuntimeException($e->getMessage());
        }
    }
}