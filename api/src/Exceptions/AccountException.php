<?php

namespace Leonardo8896\Hydrignis\Exceptions;

class AccountException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Conta inexistente ou senha incorreta.");
    }
}