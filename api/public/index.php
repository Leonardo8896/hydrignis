<?php
require_once "../vendor/autoload.php";

use Symfony\Component\Dotenv\Dotenv;

$enviroment = new Dotenv();
$enviroment->load(__DIR__.'/../.env');
