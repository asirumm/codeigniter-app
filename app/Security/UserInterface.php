<?php

namespace App\Security;

interface UserInterface
{
    public function getIdentifier(): string;
    public function getPassword(): string;
    public function getRoles();
}