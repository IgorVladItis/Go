<?php

use App\Kernel;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

$log = new Logger('Debug');
$log->pushHandler(new StreamHandler('../log_file.log', Level::Debug));
$log->pushHandler(new StreamHandler('../log_file.log', Level::Info));

$log->debug('User session', array('user' => 'customer', 'time' => date('H:i:s d.m.y')));
$log->info('Actions', array());

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};