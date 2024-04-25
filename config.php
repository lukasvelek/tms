<?php

/**
 *                              ===========================
 *                              = DATABASE SERVER ADDRESS =
 *                              ===========================
 */
define('DB_SERVER', ''); // Database server address
define('DB_USER', ''); // Database server user
define('DB_PASS', ''); // Database server user password
define('DB_NAME', ''); // Database server name


/**
 *                              =========================
 *                              = LOGGING CONFIGURATION =
 *                              =========================
 */
define('LOG_DIR', ''); // Log directory location
define('LOG_LEVEL', 0); // Log level: 0 - no log, 1 - log error only, 2 - log error & warning only, 3 - log all
define('SQL_LOG_LEVEL', 0); // SQL log level: 0 - no log, 1 - log all
define('LOG_STOPWATCH', 0); // Log stopwatch: 0 - no log, 1 - log all


/**
 *                              =========================
 *                              = CACHING CONFIGURATION =
 *                              =========================
 */
define('CACHE_DIR', ''); // Cache directory location


/**
 *                              =========================
 *                              = GENERAL CONFIGURATION =
 *                              =========================
 */
define('ID_SERVICE_USER', 1); // Service user ID
define('DEFAULT_DATETIME_FORMAT', 'Y-m-d H:i:s'); // Default datetime format
define('IS_DEBUG', false); // Is application in debug mode
define('APP_DIR', ''); // Application directory path (e.g.: C:\wwwroot\application\)
define('SERVICE_AUTO_RUN', true); // Enable service auto run
define('RELATIVE_APP_PATH', ''); // Relative application path (on server: /application/ e.g.)


/**
 *                              =========================
 *                              = MAILING CONFIGURATION =
 *                              =========================
 */
define('MAIL_SENDER_EMAIL', ''); // Email address used for sending emails
define('MAIL_SENDER_NAME', ''); // Name displayed in sent emails
define('MAIL_SERVER', ''); // Mail server address
define('MAIL_SERVER_PORT', ''); // Mail server port
define('MAIL_LOGIN_USERNAME', ''); // Mail server login username
define('MAIL_LOGIN_PASSWORD', ''); // Mail server login password


/**
 *                              ===========================
 *                              = GRID (UI) CONFIGURATION =
 *                              ===========================
 */
define('GRID_SIZE', 25); // Rows displayed in a grid


/**
 *                              =============================
 *                              = PHP RUNTIME CONFIGURATION =
 *                              =============================
 */
define('PHP_PATH', ''); // Path to the PHP directory


?>