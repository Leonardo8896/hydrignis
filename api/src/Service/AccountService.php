<?php

namespace Leonardo8896\Hydrignis\Service;

use Leonardo8896\Hydrignis\Database\Repository\UserRepository;
use Leonardo8896\Hydrignis\Exceptions\AccountException;
use Leonardo8896\Hydrignis\Model\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Leonardo8896\Hydrignis\Exceptions\FieldException;

class AccountService
{
    private User $user;
    public function __construct(
        private UserRepository $userRepository,
        private string $email,
        private ?string $name = null,
    )
    {}

    public function login(string $password): string
    {
        $this->user = $this->userRepository->searchByEmail($this->email);
        if (!$this->user) {
            throw new AccountException("Conta inexistente ou senha incorreta.");
        }

        if(!$this->user->verifyPassword($password)) {
            throw new AccountException("Conta inexistente ou senha incorreta.");
        }

        return $this->saveCredentials();
    }

    private function saveCredentials(): string
    {
        $playload = [
            "iss" => $_ENV["TOKENS_EMISSOR"],
            "iat" => time(),
            "exp" => time() + $_ENV["TOKENS_EXP"],
            "user_email" => $this->email
        ];

        return JWT::encode($playload, $_ENV["TOKENS_API"], 'HS256');
    }

    public function register(string $password): string
    {
        if ($this->userRepository->searchByEmail($this->email)) {
            throw new AccountException("Já existe uma conta com esse email");
        }

        if (!User::checkStrength($password)) {
            throw new FieldException();
        }

        $this->user = new User($this->email, $this->name, User::hashPassword($password));
        return $this->saveCredentials();
    }
}