<?php 

$bytes = openssl_random_pseudo_bytes(32);
echo bin2hex($bytes);