<?php

namespace Leonardo8896\Hydrignis\Database\Repository;

use Leonardo8896\Hydrignis\Model\User;

class UserRepository
{
    public function __construct(
        private \PDO $pdo
    ) 
    {}

    public function searchByEmail(string $email): User|null
    {
        $query = $this->pdo->prepare("SELECT * FROM USERS WHERE email= :email");
        $query->bindParam(":email", $email);
        $query->execute();
        if ($query->rowCount() === 0) {
            return null;
        }
        return $this->hydrateUser($query->fetch(\PDO::FETCH_ASSOC));
    }

    private function hydrateUser(array $user): User
    {
        return new User($user['email'], $user['name'], $user['password']);
    }

    public function save(User $user): void
    {
        $query = $this->pdo->prepare("INSERT INTO USERS (email, name, password) VALUES (:email, :name, :password)");
        $email = $user->email;
        $query->bindParam(":email", $email);
        $name = $user->name;
        $query->bindParam(":name", $name);
        $password = $user->getPassword();
        $query->bindParam(":password", $password);
        $query->execute();
    }
}