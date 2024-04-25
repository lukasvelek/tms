<?php

namespace App\Services;

use App\Core\CacheManager;
use App\Core\Logger\Logger;
use App\Models\ServiceModel;

/**
 * Abstract class that has to extend every service class.
 * It contains useful methods as well as required methods that are defined in the implemented interfaces.
 * 
 * @author Lukas Velek
 * @version 1.0
 */
abstract class AService implements IServiceRunnable {
    protected Logger $logger;
    protected CacheManager $cm;
    protected array $scfg;
    public string $name;

    /**
     * The AService constructor is used to define common instances and values.
     * 
     * @param string $name Service name (non-user-friendly, recommended to be same as the class name)
     * @param string $description Short description of the service
     * @param Logger $logger Logger instance
     * @param ServiceModel $serviceModel ServiceModel instance
     * @param CacheManager $cm CacheManager instance
     */
    protected function __construct(string $name, Logger $logger, CacheManager $cm) {
        $this->logger = $logger;
        $this->cm = $cm;
        $this->name = $name;

        $this->scfg = [];
    }

    /**
     * Logs the start of the service
     * 
     * @return void
     */
    protected function startService() : void {
        $this->logger->info('Starting service \'' . $this->name . '\'', __METHOD__);
    }

    /**
     * Logs the end of the service
     * 
     * @return void
     */
    protected function stopService() : void {
        $this->logger->info('Stopping service \'' . $this->name . '\'', __METHOD__);
    }

    /**
     * Shortcut to log a message
     * 
     * @param string $text Log message
     * @param string $method Name of the calling method (usually used: __METHOD__)
     * @return bool
     */
    protected function log(string $text, string $method) : bool {
        return $this->logger->info($text, $method);
    }

    /**
     * Returns a service stop log message
     * 
     * @return string Service stop log message
     */
    protected function getServiceStopLogMessage() {
        return 'Service ' . $this->name . ' finished running';
    }
}

?>