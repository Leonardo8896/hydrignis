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
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new FieldException();
        }

        $password = filter_input(INPUT_POST, 'password');
        if (!$password) {
            throw new FieldException();
        }

        $userRepository = new UserRepository(ConnectionCreator::createPDOConnection());
        $accountService = new AccountService($userRepository, $email);
        try {
            $token = $accountService->login($password);
        } catch (FieldException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        } catch (AccountException $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        http_response_code(200);
        echo json_encode(["Session-token" => $token]);
    }

    public static function register(): void
    {
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');
        if(!($name && $email && $password)) {
            throw new FieldException();
        }
        $userRepository = new UserRepository(ConnectionCreator::createPDOConnection());
        $accountService = new AccountService($userRepository, $email, $name);

        try {
            $token = $accountService->register($password);
        } catch (FieldException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        } catch (AccountException $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        http_response_code(200);
        echo json_encode(["Session-token" => $token]);
    }
}