<?php

namespace App\Controller\Admin;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerController
{
    public function logEvent(): Logger
    {
        $logger = new Logger("UserController");
        $logger->pushHandler(new StreamHandler(__DIR__.'Logs'.'/log_file.log', Logger::INFO));
        return $logger;
    }
}