<?php

namespace App\ApplicationConfiguration;

use App\Security\AuthProviderInterface;
use App\Security\InMemoryUserProvider;
use App\Security\UserDatabaseProvider;

/**
 * Class: SecurityConfiguration
 *
 * Konfigurasi keamanan dan autentikasi aplikasi.
 *
 * Fungsi utama:
 * 1. Menentukan credential yang digunakan untuk login (username/email).
 * 2. Menyimpan instance provider autentikasi.
 * 3. Menentukan route form login untuk redirect jika autentikasi gagal.
 */
final class SecurityConfiguration
{
    /**
     * Credential yang digunakan untuk login.
     * Bisa berupa "username" atau "email".
     * Sesuaikan dengan input name pada form login.
     */
    public static string $credential = "username";

    /** Nama input password pada form login */
    public static string $password = "password";

    /** Provider autentikasi, harus implement AuthProviderInterface */
    public static AuthProviderInterface $provider;

    /**
     * Nama route login form.
     * Digunakan oleh AuthorizationFilter untuk redirect jika login gagal.
     * Contoh:
     * $routes->get('/login', 'Home::index', ['as' => 'login_form']);
     */
    public static string $routeLoginFormName = "login_form";

    /**
     * Inisialisasi provider autentikasi default.
     * Ganti dengan provider lain jika diperlukan.
     * Method ini telah dipanggil oleh ContainerService
     */
    public static function init(): void
    {
        if (!isset(self::$provider)) {
            self::$provider = new InMemoryUserProvider();
        }
    }
}
