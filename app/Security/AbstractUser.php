<?php

namespace App\Security;

/**
 * Class: AbstractUser
 *
 * Implementasi dasar UserInterface.
 *
 * Menyimpan informasi user:
 * - username
 * - password
 * - roles (sebagai array)
 *
 * Digunakan UserService untuk verifikasi maupun membuat data payload
 */
class AbstractUser implements UserInterface
{
    private string $username;

    private string $password;

    private array $roles = [];

    public function getIdentifier(): string
    {
        return $this->username;
    }


    public function getPassword(): string
    {
        return $this->password;
    }


    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
