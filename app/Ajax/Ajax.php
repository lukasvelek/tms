<?php

use App\Core\DB\Database;
use App\Core\FileManager;
use App\Core\Logger\Logger;

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
        $dir . '\\Modules',
        $dir . '\\Ajax',
        $dir . '\\PHPMailer'
    );

    $extensionsToSkip = array(
        'html',
        'md',
        'js',
        'png',
        'gif',
        'jpg',
        'svg',
        'sql'
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

        if($filename[0] == 'A') {
            $abstractClasses[] = $dependency;
        } else if($filename[0] == 'I') {
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

loadDependencies2($dependencies, '..\\');
sortDependencies2($dependencies);

foreach($dependencies as $dependency) {
    require_once($dependency);
}

// VENDOR DEPENDENCIES

require_once('../Core/Vendor/PHPMailer/OAuthTokenProvider.php');
require_once('../Core/Vendor/PHPMailer/OAuth.php');
require_once('../Core/Vendor/PHPMailer/DSNConfigurator.php');
require_once('../Core/Vendor/PHPMailer/Exception.php');
require_once('../Core/Vendor/PHPMailer/PHPMailer.php');
require_once('../Core/Vendor/PHPMailer/POP3.php');
require_once('../Core/Vendor/PHPMailer/SMTP.php');

// END OF VENDOR DENEPENDENCIES

if(!file_exists('../../config.local.php')) {
    throw new Exception('config.local.php');
}

$user = null;

$fm = new FileManager(LOG_DIR, CACHE_DIR);

$logger = new Logger($fm);
$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_NAME, $logger);

?>