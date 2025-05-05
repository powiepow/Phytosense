<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;

$envDirectory = __DIR__ . '/../'; 
$dotenv = Dotenv::createImmutable($envDirectory);
$dotenv->load();

$secretKey = $_ENV['SECRET_KEY'];


?>