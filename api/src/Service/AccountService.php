<?php

namespace Leonardo8896\Hydrignis\Service;

use Leonardo8896\Hydrignis\Database\Repository\Interface\UserRepository;

class AccountService
{
    public function __construct(
        private UserRepository $userRepository,
        private string $email,
        private ?string $name,
    )
    {}

    public function login(string $password): void
    {
        # code...
    }
}