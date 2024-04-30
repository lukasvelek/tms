<?php

use App\Core\DB\Database;
use App\Core\FileManager;
use App\Core\Logger\Logger;
use App\Repositories\ClientRepository;
use App\Repositories\UserRepository;

session_start();

$dependencies = array();

/**
 * Creates a list of dependencies with their paths in a given directory
 * 
 * @param array $dependencies Array of dependencies
 * @param string $dir Directory to search in
 */
function loadDependencies2(array &$dependencies, string $dir) {
    $content = scandir($dir);

    unset($content[0]);
    unset($content[1]);

    $skip = array(
        $dir . '\\app_loader.php',
        $dir . '\\install',
        $dir . '\\Ajax',
        $dir . '\\PHPMailer',
        $dir . '\\Modules'
    );

    $extensionsToSkip = array(
        'html',
        'md',
        'js',
        'png',
        'gif',
        'jpg',
        'svg',
        'sql',
        'distrib'
    );

    foreach($content as $c) {
        /* SKIP TEMPLATES (html files) */
        $filenameParts = explode('.', $c);

        /* SKIP CERTAIN EXTENSIONS */
        if(in_array($filenameParts[count($filenameParts) - 1], $extensionsToSkip)) {
            continue;
        }

        $c = $dir . '\\' . $c;

        if(!in_array($c, $skip)) {
            if(!is_dir($c)) {
                // je soubor

                $dependencies[] = $c;
            } else {
                // je slozka

                loadDependencies2($dependencies, $c);
            }
        }
    }
}

/**
 * Sorts dependencies based on their type:
 *  1. Interfaces
 *  2. Abstract classes
 *  3. General classes
 * 
 * @param array $dependencies Array of dependencies
 */
function sortDependencies2(array &$dependencies) {
    $interfaces = [];
    $classes = [];
    $abstractClasses = [];

    foreach($dependencies as $dependency) {
        $filenameArr = explode('\\', $dependency);
        $filename = $filenameArr[count($filenameArr) - 1];

        if($filename[0] == 'A' && ctype_upper($filename[1])) {
            $abstractClasses[] = $dependency;
        } else if($filename[0] == 'I' && ctype_upper($filename[1])) {
            if(getNestLevel2($dependency) > 5) {
                $interfaces[] = $dependency;
            } else {
                $interfaces = array_merge([$dependency], $interfaces);
            }
        } else {
            $classes[] = $dependency;
        }
    }

    $dependencies = array_merge($interfaces, $abstractClasses, $classes);
}

/**
 * Returns the nest level of the dependency
 * 
 * @param string $dependecyPath Dependency path
 * @return int Nest level
 */
function getNestLevel2(string $dependencyPath) {
    return count(explode('\\', $dependencyPath));
}

loadDependencies2($dependencies, 'C:\\xampp\\htdocs\\tms\\app\\');
sortDependencies2($dependencies);

foreach($dependencies as $dependency) {
    require_once($dependency);
}

if(!file_exists('C:\\xampp\\htdocs\\tms\\config.local.php')) {
    throw new Exception('config.local.php');
}

require_once('C:\\xampp\\htdocs\\tms\\config.local.php');

$user = null;

$fm = new FileManager(LOG_DIR, CACHE_DIR);

$logger = new Logger($fm);
$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_NAME, $logger);

$userRepository = new UserRepository($db, $logger);
$clientRepository = new ClientRepository($db, $logger);

?>