<?php

namespace Leonardo8896\Hydrignis\Service;

use Leonardo8896\Hydrignis\Database\Repository\Interface\UserRepository;
use Leonardo8896\Hydrignis\Exceptions\AccountException;

class AccountService
{
    public function __construct(
        private UserRepository $userRepository,
        private string $email,
        private ?string $name,
    )
    {}

    public function login(string $email, string $password): void
    {
        $user = $this->userRepository->searchByEmail($email);
        if (!$user) {
            throw new AccountException();
        }

        if(!$user->verifyPassword($password)) {
            throw new AccountException();
        }
    }
}