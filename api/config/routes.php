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
    "/devices/detail/igniszero|GET" => [
        "handler" => [DevicesController::class, "detailsIgnisZero"],
        "auth" => true,
    ],
    "/devices/detail/hydralize|GET" => [
        "handler" => [DevicesController::class, "detailsHydralize"],
        "auth" => true,
    ],
    "/devices/detail/summary|GET" => [
        "handler" => [DevicesController::class, "summaryDevices"],
        "auth" => true,
    ],
    "/devices|POST"=> [
        "handler"=> [DevicesController::class, "createDevice"],
        "auth"=> true,
    ],
    "/devices/detail/igniszero/fireaccident|POST"=> [
        "handler"=> [DevicesController::class, "createFireAccident"],
        "auth"=> true,
    ],
    "/devices/detail/igniszero/gasaccident|POST"=> [
        "handler"=> [DevicesController::class, "createGasAccident"],
        "auth"=> true,
    ],
    "/devices/detail/igniszero/dailylog|POST"=> [
        "handler"=> [DevicesController::class, "createIgnisDailyLog"],
        "auth"=> true,
    ],
    "/devices/detail/hydralize/dailylog|POST"=> [
        "handler"=> [DevicesController::class, "createHydralizeDailyLog"],
        "auth"=> true,
    ],
];