<?php

namespace Leonardo8896\Hydrignis\Exceptions;

class AccountException extends \DomainException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}