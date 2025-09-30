<?php

namespace App\Security;

use App\ApplicationConfiguration\JwtConfig;

class InMemoryUserProvider implements AuthProviderInterface
{
    private array $users;

    public function __construct()
    {
        $this->users = [
//           [username, password_hash, roles]
            1 => ['username' => 'admin', 'password' => password_hash('admin', PASSWORD_BCRYPT), 'roles' => ['ROLE_ADMIN']],
            2 => ['username' => 'user', 'password' => password_hash('user', PASSWORD_BCRYPT), 'roles' => ['ROLE_USER']],
        ];
    }
    public function authenticate(string $username, string $password): ?array
    {
        foreach ($this->users as $user) {

            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                return [JwtConfig::$credentialUserPayload => $user['username'], JwtConfig::$roleUserPayload => $user['roles']];
            }
        }
        return null;
    }
}