<?php

namespace Leonardo8896\Hydrignis\Exceptions;

class FieldException extends \DomainException
{
    public function __construct()
    {
        parent::__construct("Dados da conta auxentes ou inválidos.");
    }
}