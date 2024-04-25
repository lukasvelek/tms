<?php

namespace App\Core;

use App\Authenticators\UserAuthenticator;
use App\Entities\User;
use App\Modules\IModule;
use App\Core\DB\Database;
use App\Constants\CacheCategories;
use App\Constants\FlashMessageTypes;
use App\Core\Logger\Logger;
use App\Core\FileManager;
use App\Entities\UserEntity;
use App\Helpers\ArrayStringHelper;
use App\Modules\IPresenter;
use App\Repositories\UserRepository;
use Exception;

/**
 * This is the entry point of the whole application. It contains definition for the whole frontend and backend as well.
 * All necessary classes are constructed here and kept in the variables.
 * The loaded application config file is also kept here.
 * 
 * @author Lukas Velek
 */
class Application {
    public const URL_LOGIN_PAGE = 'AnonymModule:LoginPage:showForm';
    public const URL_HOME_PAGE = 'UserModule:HomePage:showHomepage';
    public const URL_SETTINGS_PAGE = 'UserModule:Settings:showDashboard';
    public const URL_DOCUMENTS_PAGE = 'UserModule:Documents:showAll';
    public const URL_PROCESSES_PAGE = 'UserModule:Processes:showAll';
    public const URL_LOGOUT_PAGE = 'UserModule:UserLogout:logoutUser';

    public const SYSTEM_VERSION_MAJOR = 1;
    public const SYSTEM_VERSION_MINOR = 0;
    public const SYSTEM_VERSION_PATCH = 0;
    public const SYSTEM_VERSION_PATCH_DISPLAY = false;

    public const SYSTEM_IS_BETA = true;
    public const SYSTEM_VERSION = self::SYSTEM_VERSION_MAJOR . '.' . self::SYSTEM_VERSION_MINOR . (self::SYSTEM_VERSION_PATCH_DISPLAY ? ('.' . self::SYSTEM_VERSION_PATCH) : '') . (self::SYSTEM_IS_BETA ? ' beta' : '');
    public const SYSTEM_BUILD_DATE = self::SYSTEM_IS_BETA ? '- (This is beta version)' : '2024/04/02';

    public ?string $currentUrl;
    
    public IModule $currentModule;
    public IPresenter $currentPresenter;
    public string $currentAction;
    public ?int $currentIdRibbon;

    public array $pageList;
    public array $missingUrlValues;

    public ?UserEntity $user;
    public Logger $logger;
    public FileManager $fileManager;
    public ServiceManager $serviceManager;

    public UserRepository $userRepository;

    public UserAuthenticator $userAuthenticator;

    private array $modules;
    private ?string $pageContent;
    private ?string $flashMessage;

    private Database $conn;

    /**
     * This is the application class constructor. Here are all other classes constructed and assigned to their respective variables.
     * 
     * @param array $cfg The application configuration file contents
     */
    public function __construct(bool $install = true) {
        $this->currentUrl = null;
        $this->modules = [];
        $this->pageContent = null;
        $this->flashMessage = null;
        $this->pageList = [];
        $this->missingUrlValues = [];
        $this->currentIdRibbon = null;
        $this->user = null;

        $this->fileManager = new FileManager(LOG_DIR, CACHE_DIR);
        $this->logger = new Logger($this->fileManager);
        $this->conn = new Database(DB_SERVER, DB_USER, DB_PASS, DB_NAME, $this->logger);

        $this->userAuthenticator = new UserAuthenticator($this->conn, $this->logger);

        $this->userRepository = new UserRepository($this->conn, $this->logger);
        
        $sessionDestroyed = false;
        if($install) {
            $this->installDb($sessionDestroyed);
        }
        
        if($sessionDestroyed) {
            CacheManager::invalidateAllCache();
            $this->redirect(self::URL_LOGIN_PAGE);
        }

        if(SERVICE_AUTO_RUN && isset($_SESSION['id_current_user'])) {
            $this->autoRunServices();
        }
    }

    /**
     * Redirects the application page to different page using constructed URL that is based on passed parameters.
     * 
     * @param string $url The default page URL
     * @param array $params All other parameters that should be passed to the presenter
     */
    public function redirect(string $url, array $params = array(), string $hashtag = '') {
        $page = '?';

        if(isset($_GET['page'])) {
            $urlPage = htmlspecialchars($_GET['page']);
            $urlPageParts = explode(':', $urlPage);

            if($url == ':') {
                $url = $urlPage;
            } else {
                $vals = explode(':', $url);

                switch(count($vals)) {
                    case 1:
                        $url = $urlPageParts[0] . ':' . $urlPageParts[1] . ':' . $url;
                        break;
            
                    case 2:
                        $url = $urlPageParts[0] . ':' . $url;
                        break;
                }
            }
        }

        $newParams = array('page' => $url);

        foreach($params as $k => $v) {
            if($k == 'page') continue;

            $newParams[$k] = $v;
        }

        if(!array_key_exists('id_ribbon', $newParams) && $url != self::URL_LOGIN_PAGE) {
            if(isset($_SESSION['id_current_ribbon'])) {
                $newParams['id_ribbon'] = $this->currentIdRibbon;
            }
        }

        if($url != self::URL_LOGIN_PAGE) {
            if(array_key_exists('id_current_ribbon', $_SESSION)) {
                unset($_SESSION['id_current_ribbon']);
            }
        }

        $i = 0;
        foreach($newParams as $paramKey => $paramValue) {
            if(($i + 1) == count($newParams)) {
                $page .= $paramKey . '=' . $paramValue;
            } else {
                $page .= $paramKey . '=' . $paramValue . '&';
            }

            $i++;
        }

        if($hashtag != '') {
            if(!str_contains($hashtag, '#')) {
                $hashtag = '#' . $hashtag;
            }
        }

        $page .= $hashtag;

        header('Location: ' . $page);
        exit;
    }

    /**
     * Shows the current page to the user
     */
    public function showPage() {
        if(is_null($this->pageContent)) {
            $this->renderPage();
        }

        echo $this->pageContent;
    }

    /**
     * Renders the current page and saves it to the $pageContent variable
     */
    public function renderPage() {
        if(is_null($this->currentUrl)) {
            throw new Exception('$currentUrl');
        }


        // --- PANELS ---

        //$toppanel = $this->renderToppanel();
        //$subpanel = $this->renderSubpanel();

        // --- END OF PANELS ---

        
        // URL Link: Module:Presenter:action
        $parts = explode(':', $this->currentUrl);
        $module = $parts[0];
        $presenter = $parts[1];
        $action = $parts[2];

        // Get current action
        $this->currentAction = $parts[2];

        // Get module
        if(array_key_exists($module, $this->modules)) {
            $this->currentModule = $module = $this->modules[$module];
        } else {
            throw new Exception('Module ' . $module);
        }

        // Get presenter
        $origPresenter = $presenter;
        $presenter = $module->getPresenterByName($presenter . 'Presenter');

        if($presenter === NULL) {
            throw new Exception('Presenter ' . $origPresenter);
        }

        $this->currentPresenter = $presenter;
        $module->setPresenter($presenter);

        // User is allowed to visit specific pages before logging in
        if($this->currentPresenter->allowWhenLoginProcess === false && isset($_SESSION['login_in_process'])) {
            $this->flashMessage('You have to be logged in in order to visit this page.', 'warn');
            $this->redirect(self::URL_LOGIN_PAGE);
        }

        // Load page body
        try {
            $pageBody = $module->currentPresenter->performAction($action);
        } catch(Exception $e) {
            $this->flashMessage($e->getMessage() . ' Stack trace: ' . $e->getTraceAsString(), 'error');
            $this->redirect(self::URL_HOME_PAGE);
        }


        // --- PAGE CONTENT CREATION ---

        $this->pageContent = '';

        /*if($presenter::DRAW_TOPPANEL) {
            $this->pageContent .= $toppanel;
        }

        if(!is_null($subpanel)) {
            $this->pageContent .= $subpanel;
        }*/

        $this->renderFlashMessage();

        $this->pageContent .= $pageBody;

        // --- END OF PAGE CONTENT CREATION ---


        if(str_contains($action, 'show')) {
            $this->clearFlashMessage();
        }
    }

    /**
     * Renders a flash message
     * 
     * @return void
     */
    public function renderFlashMessage() {
        if($this->flashMessage != null) {
            $this->pageContent .= $this->flashMessage;
            return;
        }

        $cm = CacheManager::getTemporaryObject(CacheCategories::FLASH_MESSAGES);

        $valFromCache = $cm->loadFlashMessage();

        $createCode = function($message, $type, $index) {
            $code = '<div id="flash-message-' . $index . '" class="' . $type . '">';
            $code .= '<div class="row">';
            $code .= '<div class="col-md">';
            $code .= $message;
            $code .= '</div>';
            $code .= '<div class="col-md-1" id="right">';
            $code .= '<a style="cursor: pointer" onclick="hideFlashMessage(\'' . $index . '\')">x</a>';
            $code .= '</div>';
            $code .= '</div>';
            $code .= '</div>';

            return $code;
        };

        if(!is_null($valFromCache)) {
            $this->flashMessage = '';

            $i = 0;
            foreach($valFromCache as $msg) {
                $message = $msg['message'];
                $type = $msg['type'];

                $this->flashMessage .= $createCode($message, $type, $i);

                $i++;
            }

            $this->pageContent .= $this->flashMessage;
        }

        $this->clearFlashMessage();

        return;
    }

    /**
     * Renders the toppanel
     * 
     * @return string HTML code of the toppanel
     */
    public function renderToppanel() {
        $panel = '';

        return $panel;
    }

    /**
     * Renders the subpanel
     * 
     * @return string HTML code of the subpanel
     */
    public function renderSubpanel() {
        $panel = '';

        return $panel;
    }

    /**
     * Registers the passed module to the module system
     * 
     * @param IModule $module Module to be saved
     */
    public function registerModule(IModule $module) {
        $this->modules[$module->getName()] = $module;
    }

    /**
     * Returns a component based on its name
     * 
     * @param string $name Component name
     * @param mixed|null Mixed if the component exists and null if it does not exist
     */
    public function getComponent(string $name) {
        if(isset($this->$name)) {
            return $this->$name;
        } else {
            return null;
        }
    }

    /**
     * Sets the current user
     * 
     * @param User $user Current user
     */
    public function setCurrentUser(UserEntity $user) {
        $this->user = $user;
    }

    /**
     * Returns the database connection
     * 
     * @return Database $conn Database connection
     */
    public function getConn() {
        return $this->conn;
    }

    /**
     * Flashes a message to the user
     * 
     * @param string $message Message text
     * @param string $type Message type (options defined in App\Constants\FlashMessageTypes)
     */
    public function flashMessage(string $message, string $type = FlashMessageTypes::INFO) {
        $cm = CacheManager::getTemporaryObject(CacheCategories::FLASH_MESSAGES);
        $cm->saveFlashMessage(array('message' => $message, 'type' => $type));
    }

    /**
     * Clears a flash message
     */
    public function clearFlashMessage() {
        $this->flashMessage = null;

        $cm = CacheManager::getTemporaryObject(CacheCategories::FLASH_MESSAGES);
        $cm->invalidateCache();
    }

    /**
     * Loads a list of pages available to be set as default. 
     * Nothing is returned because it is saved to cache.
     */
    public function loadPages() {
        $pcm = CacheManager::getTemporaryObject(CacheCategories::PAGES);

        $cachePages = $pcm->loadStringsFromCache();

        if(!is_null($cachePages) || $cachePages === FALSE) {
            $this->pageList = $cachePages;
        } else {
            foreach($this->modules as $module) {
                if(in_array($module->getName(), array('AnonymModule'))) continue;
    
                foreach($module->getPresenters() as $presenter) {
                    foreach($presenter->getActions() as $realAction => $action) {
                        $page = $module->getName() . ':' . $presenter->getName() . ':' . $action;
                        $realPage = $module->getName() . ':' . $presenter->getName() . ':' . $realAction;

                        $this->pageList[$realPage] = $page;
                    }
                }
            }

            $pcm->saveArrayToCache($this->pageList);
        }
    }

    /**
     * Checks is passed parameters exist in the global variables $_POST and $_GET. If one of the passed is missing it returns false, otherwise true.
     * 
     * @param array $values Values to be checked
     * @return bool True if all exist or false if one or more do not exist
     */
    public function isset(...$values) {
        $present = true;

        foreach($values as $value) {
            if(!isset($_POST[$value]) && !isset($_GET[$value])) {
                $this->missingUrlValues[] = $value;
                $present = false;
            }
        }

        return $present;
    }

    /**
     * Flashes a message to the user that one of given values is missing in the $_GET or $_POST global variables.
     * 
     * @param array $values Values to be checked
     * @param bool $redirect True if the page should be redirected automatically
     * @param array|null $redirectUrl The URL where should the page redirect to
     */
    public function flashMessageIfNotIsset(array $values, bool $redirect = true, ?array $redirectUrl = []) {
        $present = true;

        foreach($values as $value) {
            if(!isset($_POST[$value]) && !isset($_GET[$value])) {
                $this->missingUrlValues[] = $value;
                $present = false;
            }
        }

        if(!$present) {
            $this->flashMessage('These values: ' . ArrayStringHelper::createUnindexedStringFromUnindexedArray($this->missingUrlValues, ',') . ' are missing!', 'error');
            
            if($redirect) {
                if(is_null($redirectUrl) || empty($redirectUrl)) {
                    $this->redirect(self::URL_HOME_PAGE);
                } else {
                    $this->redirect($redirectUrl['page'], $redirectUrl);
                }
            }
        }
    }

    public function throwError(string $message, array $redirectUrl = ['page' => 'UserModule:HomePage:showHomepage']) {
        $this->flashMessage($message, 'error');
        $this->redirect($redirectUrl['page'], $redirectUrl);
    }

    /**
     * Performs the initial database installation.
     * After installing, it creates a file that shows whether the database has been installed or not.
     */
    private function installDb(bool &$sessionDestroyed) {
        if(!file_exists('app/core/install')) {
            $conn = $this->conn;

            $this->logger->logFunction(function() use ($conn) {
                $conn->installer->install();
            }, __METHOD__);

            file_put_contents('app/core/install', 'installed');

            session_destroy();
        }
    }

    /**
     * Automatically runs scheduled services
     */
    private function autoRunServices() {
        
    }
}

?>