<?php

namespace App\Core;

use App\Authorizators\DocumentAuthorizator;
use App\Authorizators\DocumentBulkActionAuthorizator;
use App\Components\DocumentLockComponent;
use App\Components\DocumentReportGeneratorComponent;
use App\Components\NotificationComponent;
use App\Components\ProcessComponent;
use App\Constants\CacheCategories;
use App\Core\DB\Database;
use App\Core\Logger\Logger;
use App\Models\DocumentMetadataHistoryModel;
use App\Models\DocumentModel;
use App\Models\FileStorageModel;
use App\Models\GroupModel;
use App\Models\GroupUserModel;
use App\Models\MailModel;
use App\Models\NotificationModel;
use App\Models\ServiceModel;
use App\Models\UserModel;
use App\Repositories\DocumentCommentRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\UserAbsenceRepository;
use App\Repositories\UserRepository;
use App\Services\DeclinedDocumentRemoverService;
use App\Services\DocumentArchivationService;
use App\Services\DocumentReportGeneratorService;
use App\Services\ExtractionService;
use App\Services\FileManagerService;
use App\Services\LogRotateService;
use App\Services\MailService;
use App\Services\NotificationManagerService;
use App\Services\ShreddingSuggestionService;
use App\Services\UserLoginBlockingManagerService;
use App\Services\UserSubstitutionProcessService;

/**
 * Manager responsible for services
 * 
 * @author Lukas Velek
 */
class ServiceManager {
    private Logger $logger;
    private CacheManager $cm;
    private FileManager $fm;

    private array $runDates;

    public array $services;

    /**
     * Class constructor
     * 
     * @param Logger $logger Logger instance
     * @param ServiceModel $serviceModel ServiceModel instance
     * @param FileStorageManager $fsm FileStorageManager instance
     * @param DocumentModel $documentModel DocumentModel instance
     * @param CacheManager $cm CacheManager instance
     * @param DocumentAuthorizator $documentAuthorizator DocumentAuthorizator instance
     * @param ProcessComponent $processComponent ProcessComponent instance
     * @param UserModel $userModel UserModel instance
     * @param GroupUserModel $groupUserModel GroupUserModel instance
     * @param MailModel $mailModel MailModel instance
     * @param MailManager $mailManager MailManager instance
     * @param NotificationModel $notificationModel NotificationModel instance
     * @param DocumentReportGeneratorComponent $documentReportGeneratorComponent DocumentReportGeneratorComponent instance
     * @param NotificationComponent $notificationComponent NotificationComponent instance
     * @param DocumentMetadataHistoryModel $dmhm DocumentMetadataHistoryModel instance
     * @param DocumentLockComponent $dlc DocumentLockComponent instance
     */
    public function __construct(Logger $logger,
                                CacheManager $cm,
                                FileManager $fm) {
        $this->logger = $logger;
        $this->cm = $cm;
        $this->fm = $fm;
        
        $this->loadServices();
    }

    /**
     * Starts a background service asynchronously
     * 
     * @param string $serviceName Service name
     * @return true
     */
    public function startBgProcess(string $serviceName) {
        $phpExe = PHP_PATH . 'php.exe';

        $serviceFile = APP_DIR . 'services\\' . $serviceName . '.php';

        $cmd = $phpExe . ' ' . $serviceFile;

        if(substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B ". $cmd, "w")); 
        } else {
            exec($cmd . " > /dev/null &");  
        }

        return true;
    }

    /**
     * Returns service by its name
     * 
     * @param string $name Service name
     * @return null|AService Service instance or null
     */
    public function getServiceByName(string $name) {
        foreach($this->services as $k => $v) {
            if($v->name == $name) {
                return $v;
            }
        }

        return null;
    }

    /**
     * Returns last run date for a service
     * 
     * @param string $name Service name
     * @return string Run date or dash
     */
    public function getLastRunDateForService(string $name) {
        if(array_key_exists($name, $this->runDates)) {
            if(array_key_exists('last_run_date', $this->runDates[$name])) {
                return $this->runDates[$name]['last_run_date'];
            } else {
                return '-';
            }
        } else {
            return '-';
        }
    }

    /**
     * Returns next run date for a service
     * 
     * @param string $name Service name
     * @return string Run date or dash
     */
    public function getNextRunDateForService(string $name) {
        if(array_key_exists($name, $this->runDates) && array_key_exists('last_run_date', $this->runDates[$name]) && array_key_exists('next_run_date', $this->runDates[$name])) {
            return $this->runDates[$name]['next_run_date'];
        } else {
            return '-';
        }
    }

    /**
     * Creates service instances
     * 
     * To disable service, comment the service line that saves instance to the ServiceManager::services array
     */
    private function loadServices() {
    }
}

?>