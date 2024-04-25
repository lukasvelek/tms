<?php

use App\Core\AppConfiguration;
use App\Core\DB\Database;
use App\Core\FileManager;
use App\Core\Logger\Logger;
use App\Core\Logger\LogFileTypes;

require_once('App/App_loader.php');

$fm = new FileManager(LOG_DIR, CACHE_DIR);
$logger = new Logger($fm);
$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_NAME, $logger);

function start(string $name) {
    global $serviceModel, $logger;

    $service = $serviceModel->getServiceByName($name);
    $serviceModel->updateService($service->getId(), ['status' => '1', 'pid' => getmypid()]);
    $logger->info('Service ' . $name . ' start...');
    $logger->setType(LogFileTypes::SERVICE); // will switch logging to service log file
}

function stop(string $name) {
    global $serviceModel, $logger;
    
    $service = $serviceModel->getServiceByName($name);
    $serviceModel->updateService($service->getId(), ['status' => '0', 'pid' => NULL]);
    $logger->setType(LogFileTypes::DEFAULT); // will switch logging back to normal log file
    $logger->info('Service ' . $name . ' stop...');
}

function run(callable $run) {
    global $logger;

    $result = true;

    try {
        $run();
    } catch(Exception $e) {
        $logger->error($e->getMessage() . ' - Trace: ' . $e->getTraceAsString(), __METHOD__);
        $result = false;
    }

    return $result;
}

?>