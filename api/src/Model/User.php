<?php

namespace Leonardo8896\Hydrignis\Model;

class User
{
    public function __construct(
        public readonly string $email,
        public readonly string $name,
        private string $password_hash
    )
    {}

    public function verifyPassword(string $password): bool
    {
        //Implementar rehash
        return password_verify($password, $this->password_hash);
    }

    public function getPassword(): string
    {
        return $this->password_hash;
    }

    static public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }

    static public function checkStrength(string $password): bool
    {
        return strpbrk($password, "ABCDEFGHIJKLMNOPQRSTUVWXYZ") 
            && strpbrk($password, "!@#$%&*?")
            && strlen($password) >= 8;
    }
}