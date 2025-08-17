<?php
namespace Leonardo8896\Hydrignis\Controllers;
use Leonardo8896\Hydrignis\Exceptions\FieldException;
use Leonardo8896\Hydrignis\Exceptions\AccountException;
use Leonardo8896\Hydrignis\Service\AccountService;
use Leonardo8896\Hydrignis\Database\Core\ConnectionCreator;
use Leonardo8896\Hydrignis\Database\Repository\Interface\UserRepository as InterfaceUserRepository;
use Leonardo8896\Hydrignis\Database\Repository\UserRepository;

class AccountController
{
    public static function login(): void
    {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new FieldException("Invalid email format.");
        }

        $password = filter_input(INPUT_POST, 'password');
        if (!$password) {
            throw new FieldException("Password cannot be empty.");
        }

        $userRepository = new InterfaceUserRepository(ConnectionCreator::createPDOConnection());
        $accountService = new AccountService($userRepository, $email, $password);
        try {
            $accountService->login($email, $password);
        } catch (FieldException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        } catch (AccountException $e) {
            http_response_code(401);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }
    }

    public static function register(): void
    {
        
    }
}