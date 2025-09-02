<?php
namespace Leonardo8896\Hydrignis\Controllers;
use Leonardo8896\Hydrignis\Exceptions\FieldException;
use Leonardo8896\Hydrignis\Exceptions\AccountException;
use Leonardo8896\Hydrignis\Service\AccountService;
use Leonardo8896\Hydrignis\Database\Core\ConnectionCreator;
use Leonardo8896\Hydrignis\Database\Repository\UserRepository;

class AccountController
{
    public static function login(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Preencha o email corretamente']);
            return;
        }
        if (!$password) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Preencha a senha']);
            return;
        }

        $userRepository = new UserRepository(ConnectionCreator::createPDOConnection());
        $accountService = new AccountService($userRepository, $email);
        try {
            $token = $accountService->login($password);
        } catch (AccountException $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(["Session-token" => $token]);
    }

    public static function register(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if(!($name && $email && $password)) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Preencha todos os campos corretamente']);
            return;
        }
        $userRepository = new UserRepository(ConnectionCreator::createPDOConnection());
        $accountService = new AccountService($userRepository, $email, $name);

        try {
            $token = $accountService->register($password);
        } catch (AccountException $e) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(["Session-token" => $token]);
    }
}