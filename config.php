<?php

$dev_mode = env('DEV_MODE', false) ? true : false;
define('ERROR_REPORTING', $dev_mode);
define('DISPLAY_ERRORS', $dev_mode);

$GLOBALS['config']['app_name'] = env('APP_NAME', 'Editora Dialética');
$GLOBALS['config']['editor_name'] = env('APP_EDITOR_NAME', 'Lucas Martins');

# file size mb limit
$GLOBALS['config']['article_max_file_size'] = env('ARTICLE_MAX_FILE_SIZE', 10);

// Database connection
$GLOBALS['config']['database'] = [
    'profile' => env('DB_PROFILE', false),
    'fetch' => PDO::FETCH_CLASS,
    'default' => 'app',
    'connections' => [
        'app' => [
            'driver'   => 'mysql',
            'host'     => env('DB_HOST'),
            'port'     => env('DB_PORT'),
            'username' => env('DB_USER'),
            'password' => env('DB_PASS'),
            'database' => env('DB_DATABASE'),
            'charset'  => 'utf8',
            'options' => [
                PDO::ATTR_CASE => PDO::CASE_NATURAL,
            ]
        ]
    ],
];

// Hash and crypt algo key
$GLOBALS['config']['app_key'] = env('APP_KEY');

// SMTP
$GLOBALS['config']['smtp'] = [
    'host' => env('SMTP_HOST'),
    'port' => env('SMTP_PORT'),
    'auth' => env('SMTP_AUTH'),
    'secure' => env('SMTP_SECURE'),
    'sender_email' => env('SMTP_SENDER_EMAIL'),
    'sender_user' => env('SMTP_SENDER_USER'),
    'sender_pass' => env('SMTP_SENDER_PASS'),
    'sender_name' => env('SMTP_SENDER_NAME'),
];

#PLUG NOTAS
$GLOBALS['config']['plug_notas_cpf_cnpj'] = env('PLUG_NOTAS_CPF_CNPJ');
$GLOBALS['config']['plug_notas_email'] = env('PLUG_NOTAS_EMAIL');
$GLOBALS['config']['plug_notas_sandbox'] = env('PLUG_NOTAS_SANDBOX', false);
if ($GLOBALS['config']['plug_notas_sandbox']) {
    $GLOBALS['config']['plug_notas_api_key'] = env('PLUG_NOTAS_SANDBOX_API_KEY');
} else {
    $GLOBALS['config']['plug_notas_api_key'] = env('PLUG_NOTAS_API_KEY');
}

#CIELO
$GLOBALS['config']['cielo_sandbox'] = env('CIELO_SANDBOX', false);
if ($GLOBALS['config']['cielo_sandbox']) {
    $GLOBALS['config']['cielo_merchant_id'] = env('CIELO_SANDBOX_MERCHANT_ID');
    $GLOBALS['config']['cielo_merchant_key'] = env('CIELO_SANDBOX_MERCHANT_KEY');
    $GLOBALS['config']['cielo_reviews_merchant_id'] = env('CIELO_REVIEWS_SANDBOX_MERCHANT_ID');
    $GLOBALS['config']['cielo_reviews_merchant_key'] = env('CIELO_REVIEWS_SANDBOX_MERCHANT_KEY');
} else {
    $GLOBALS['config']['cielo_merchant_id'] = env('CIELO_MERCHANT_ID');
    $GLOBALS['config']['cielo_merchant_key'] = env('CIELO_MERCHANT_KEY');
    // $GLOBALS['config']['cielo_reviews_merchant_id'] = env('CIELO_MERCHANT_ID');
    // $GLOBALS['config']['cielo_reviews_merchant_key'] = env('CIELO_MERCHANT_KEY');

    $GLOBALS['config']['cielo_reviews_merchant_id'] = env('CIELO_REVIEWS_MERCHANT_ID');
    $GLOBALS['config']['cielo_reviews_merchant_key'] = env('CIELO_REVIEWS_MERCHANT_KEY');
}


# SPACES
$GLOBALS['config']['spaces'] = [
    'key'    => env('DO_KEY'),
    'secret' => env('DO_SECRET'),
    'space'  => env('DO_BUCKET'),
    'region' => env('DO_REGION'),
];
