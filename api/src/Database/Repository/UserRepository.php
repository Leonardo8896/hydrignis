<?php

namespace Leonardo8896\Hydrignis\Database\Repository\Interface;

use Leonardo8896\Hydrignis\Model\User;

class UserRepository
{
    public function __construct(
        private \PDO $pdo
    ) 
    {}

    public function searchByEmail(string $email): User|null
    {
        $query = $this->pdo->prepare("SELECT * FROM USERS WHERE rm= :rm");
        $query->bindParam(":rm", $email);
        $query->execute();

        return $this->hydrateUser($query->fetch(\PDO::FETCH_ASSOC));
    }

    private function hydrateUser(array $user): User
    {
        return new User($user['email'], $user['name'], $user['password']);
    }
}