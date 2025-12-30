<?php

use iutnc\SAE_APP_WEB\Dispatch;

require __DIR__ . '/vendor/autoload.php'; 
session_start();



if (strpos(__DIR__, '/users/home/name') !== false) {
    // Webetu
    $configPath = '/users/home/name/config/db.config.ini';
} else {
    // localhost
    $configPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'db.config.ini';
}


iutnc\SAE_APP_WEB\repository\Repository::setConfig($configPath);


$action = $_GET['action'] ?? 'default';
$dispatcher = new Dispatch\Dispatcher($action);
$dispatcher->run();
