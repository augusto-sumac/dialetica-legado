<?php

setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

/**
 * TimeZone
 */
date_default_timezone_set('America/Sao_Paulo');

define('DS', DIRECTORY_SEPARATOR);
define('MB_STRING', (int) function_exists('mb_get_info'));

/**
 * Caminho absoluto para a pasta atual
 */
define('ROOT_PATH', rtrim(dirname(__DIR__), DS) . DS);

/**
 * Caminho absoluto para a pasta atual
 */
define('APP_PATH', ROOT_PATH . 'app' . DS);

/**
 * Caminho para a pasta de views
 */
define('VIEW_PATH', APP_PATH);

/**
 * Caminho para a pasta de views
 */
define('STORAGE_PATH', ROOT_PATH . 'storage' . DS);

/**
 * Autoload
 */
require_once(ROOT_PATH . '/vendor/autoload.php');

/**
 * Env Files
 */
require_once(ROOT_PATH . '/vendor/DotEnv/DotEnv.php');

$env_file = file_exists(ROOT_PATH . '/.env.dev') ? 'env.dev' : 'env';

(new DotEnv(ROOT_PATH . "/.{$env_file}"))->load();

/**
 * Helpers
 */
require_once(ROOT_PATH . '/vendor/helpers.php');

/**
 * Application config
 */
require_once(ROOT_PATH . '/config.php');

// Error Handler

error_reporting(E_ALL);
ini_set('display_errors', 'on');

/**
 * App Classes
 */
require_once(ROOT_PATH . '/vendor/database.php');
require_once(ROOT_PATH . '/vendor/InsertOrUpdateMany.php');
require_once(ROOT_PATH . '/vendor/str.php');
require_once(ROOT_PATH . '/vendor/crypter.php');
require_once(ROOT_PATH . '/vendor/hash.php');
require_once(ROOT_PATH . '/vendor/validator.php');
require_once(ROOT_PATH . '/vendor/file.php');
require_once(ROOT_PATH . '/vendor/blade.php');

if (file_exists(ROOT_PATH . 'app_helpers.php')) {
    require_once(ROOT_PATH . 'app_helpers.php');
}

require_once(__DIR__ . '/helpers.php');
