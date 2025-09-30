<?php

namespace App\Security;

/**
 * Interface: AuthProviderInterface
 *
 * Interface untuk provider autentikasi.
 *
 * Fungsi utama:
 * - Menyediakan metode `authenticate` untuk memverifikasi username dan password.
 * - Mengembalikan data user jika autentikasi berhasil, atau null jika gagal.
 *
 * Contoh implementasi:
 * - InMemoryUserProvider
 * - UserDatabaseProvider
 */
interface AuthProviderInterface
{
    /**
     * Autentikasi user berdasarkan username dan password.
     *
     * @param string $username Username atau credential login lainnya
     * @param string $password Password user
     * @return array|null Array berisi data user (id, username, roles[]) jika berhasil,
     *                    atau null jika autentikasi gagal
     */
    public function authenticate(string $username, string $password): ?array;
}
