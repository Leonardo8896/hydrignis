<?php

use Leonardo8896\Hydrignis\Controllers\{
    TestServerController,
    AccountController
};

require_once "../vendor/autoload.php";

return [
    "/|GET" => [TestServerController::class, "index"],
    "/account/login|POST" => [AccountController::class, "login"],
    "/account/register|POST" => [AccountController::class, "register"]
];