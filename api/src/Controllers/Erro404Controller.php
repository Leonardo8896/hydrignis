<?php

namespace Leonardo8896\Hydrignis\Controllers;

class Erro404Controller
{
    public static function index(): void
    {
        http_response_code(404);
    }
}