<?php

namespace App\ApplicationConfiguration;

use Google_Client;

/**
 * Class: GoogleOauthConfig
 *
 * Konfigurasi OAuth untuk login menggunakan Google (Gmail).
 *
 * Fungsi utama:
 * 1. Mengatur redirect URI sesuai aplikasi.
 * 2. Mengatur Client ID dan Client Secret dari environment.
 * 3. Menentukan scope yang diperlukan (misal: email).
 * 4. Mengatur akses offline agar bisa mendapatkan refresh token.
 *
 * Contoh penggunaan:
 * $googleClient = GoogleOauthConfig::getConfig();
 */
class GoogleOauthConfig
{
    public static function getConfig(): Google_Client
    {
        $client = new Google_Client();

        // URL callback sesuai konfigurasi Google Console
        $client->setRedirectUri(base_url('api/login/callback'));

        // credential dari environment
        $client->setClientId(getenv('google.oauth.client.id'));
        $client->setClientSecret(getenv('google.oauth.client.secret'));

        // Scope yang dibutuhkan (email user)
        $client->setScopes('email');

        // Agar bisa mendapatkan refresh token
        $client->setAccessType('offline');
        $client->setPrompt('consent select_account');

        return $client;
    }
}
