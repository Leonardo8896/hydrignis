<?php

use Leonardo8896\Hydrignis\Controllers\{
    TestServerController,
    AccountController,
    DevicesController
};

require_once "../vendor/autoload.php";

return [
    "/|GET" => [
        "handler" => [TestServerController::class, "index"],
        "auth" => false,
    ],
    "/account/login|POST" => [
        "handler" => [AccountController::class, "login"],
        "auth" => false,
    ],
    "/account/register|POST" => [
        "handler" => [AccountController::class, "register"],
        "auth" => false,
    ],
    "/devices|GET" => [
        "handler" => [DevicesController::class, "index"],
        "auth" => true,
    ],
    "/devices/detail|GET" => [
        "handler" => [DevicesController::class, "show"],
        "auth" => true,
    ],
];