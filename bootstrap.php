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
define('ROOT_PATH', rtrim(dirname(__FILE__), DS) . DS);

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
require_once(__DIR__ . '/vendor/autoload.php');

/**
 * Env Files
 */
require_once(__DIR__ . '/vendor/DotEnv/DotEnv.php');

$env_file = file_exists(__DIR__ . '/.env.dev') ? 'env.dev' : 'env';
(new DotEnv(__DIR__ . "/.{$env_file}"))->load();

/**
 * Helpers
 */
require_once(__DIR__ . '/vendor/helpers.php');

/**
 * Application config
 */
require_once('config.php');

// Error Handler

error_reporting(ERROR_REPORTING ? E_ALL : 0);
ini_set('display_errors', DISPLAY_ERRORS ? 'on' : 'off');

/**
 * App Classes
 */
require_once(__DIR__ . '/vendor/event.php');
require_once(__DIR__ . '/vendor/database.php');
require_once(__DIR__ . '/vendor/model.php');
require_once(__DIR__ . '/vendor/InsertOrUpdateMany.php');
require_once(__DIR__ . '/vendor/str.php');
require_once(__DIR__ . '/vendor/crypter.php');
require_once(__DIR__ . '/vendor/hash.php');
require_once(__DIR__ . '/vendor/validator.php');
require_once(__DIR__ . '/vendor/request.php');
require_once(__DIR__ . '/vendor/response.php');
require_once(__DIR__ . '/vendor/router.php');
require_once(__DIR__ . '/vendor/file.php');
require_once(__DIR__ . '/vendor/blade.php');
require_once(__DIR__ . '/vendor/cookie.php');
require_once(__DIR__ . '/vendor/spaces.php');

if (file_exists(ROOT_PATH . 'constants.php')) {
    require_once(ROOT_PATH . 'constants.php');
}

if (file_exists(ROOT_PATH . 'app_helpers.php')) {
    require_once(ROOT_PATH . 'app_helpers.php');
}

/**
 * Display only basic error message
 *
 * @param integer $code
 * @param boolean $stop
 * @return void
 */
function stop_on_error_reporting_none($code, $message, $stop = false)
{
    if ($stop || error_reporting() === 0) {
        if (isAjax()) {
            header('Content-Type: application/json');
            exit('{"code":"' . $code . '","message":"' . $message . '"}');
        }

        exit($code . ' - ' . $message);
    }
}

/**
 * Global exception handler to error reporting
 * @param  object $exception
 * @return void
 */
function error_exception($exception)
{
    $code = $exception->getCode();

    if ($code < 300) {
        $code = 500;
    }

    $log_dir = STORAGE_PATH . 'logs' . DS . 'errors';
    if (!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }

    $log_file = $log_dir . DS . date('Y-m-d') . '.log';
    file_put_contents($log_file, implode("\n", [
        date('Y-m-d H:i:s') . ' - ' . $code . ' - ' . $exception->getMessage(),
        'Error occurred on line ' . $exception->getLine() . ' of file ' . $exception->getFile(),
        'With trace',
        $exception->getTraceAsString()
    ]) . "\n\n", FILE_APPEND);

    if ($exception instanceof Responsable) {
        sapi_emit($exception->getResponse());
        exit(0);
    }

    if (!DISPLAY_ERRORS) {
        $response = isAjax() ? response_json_fail('500 - Internal Server Error', ['status_code' => 500], 500) : response_html(view('500'), 500);
        sapi_emit($response);
        exit(0);
    }

    http_response_code((int) $code);
    stop_on_error_reporting_none($code, $exception->getMessage(), preg_match('/mysql_con|_mysql/i', $exception->getFile()));

    if (isAjax()) {
        if (!is_cli()) {
            header('Content-Type: application/json');
        }

        $json_trace = preg_replace(
            '/PDO->__construct\(+(.*)+\)/i',
            'PDO->__construct(...)',
            json_encode(
                array_map(function ($line) {
                    if (isset($line['args'])) {
                        unset($line['args']);
                    }
                    return $line;
                }, $exception->getTrace())
            )
        );

        echo json_encode([
            'code' => $code,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'message' => $exception->getMessage(),
            'trace' => json_decode($json_trace)
        ]);
    } else {
        if (php_sapi_name() !== "cli") {
            header('Content-Type: text/html');
        }

        $traceString = $exception->getTraceAsString();
        $traceString = preg_replace('/PDO->__construct\(+(.*)+\)/i', 'PDO->__construct(...)', $traceString);

        echo '
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>' . $exception->getMessage() . '</title>
            </head>
            <body>
            <h2>Unhandled Exception</h2>
            <h3>Message:</h3>
            <pre>' . $exception->getMessage() . '</pre>
            <h3>Location:</h3>
            <pre>' . $exception->getFile() . ' on line ' . $exception->getLine() . '</pre>
            <h3>Stack Trace:</h3>
            <pre>' . $traceString . '</pre>
            </body>
            </html>
        ';
    }

    exit(0);
}

/**
 * Exception handler
 */
set_exception_handler(function ($exception) {
    error_exception($exception);
});

/**
 * Error handler
 */
set_error_handler(function ($code, $error, $file, $line) {
    error_exception(new ErrorException($error, $code, 0, $file, $line));
});

/**
 * Shutdown function
 */
register_shutdown_function(function () {
    $error = error_get_last();
    if (!is_null($error)) {
        extract($error, EXTR_SKIP);
        error_exception(new ErrorException($message, $type, 0, $file, $line));
    }
});

if (!is_cli()) {
    /**
     * Sessions
     */
    session_start();
}

/**
 * Translate @set(varName, value) => $varName = value;
 */
Blade::extend(function ($value = '') {
    return preg_replace("/@set\('(.*?)'\,(.*)\)/", '<?php $$1 = $2; ?>', $value);
});
