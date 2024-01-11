<?php

use GuzzleHttp\Psr7\Response;

$version = substr(str_replace('.', '', phpversion()), 0, 2);
if (strlen($version) === 1) {
    $version = $version . '0';
}
if ((int)$version < 74) {
    die('Esta aplicação precisa da versão 7.4+ do php para funcionar. O servidor está rodando a versão: ' . phpversion());
}

// exit('Aguarde! Sistema em manutenção!');

require_once('bootstrap.php');

// Define app version for assets
define('APP_VERSION', 202211010214); // time()

// Maintenance mode
Route::any('/maintenance', function () {
    return view('layouts.maintenance');
});

// Filters
Route::filter('is-logged', function () {
    if (!preg_match('/\/auth/i', uri_path())) {
        $isAdmin = !empty(array_get($_SESSION, 'user'));
        $isAuthor = !empty(array_get($_SESSION, 'author'));

        if (!$isAdmin && !$isAuthor) {
            return redirect_to_author_login();
        }
    }
});

if (!preg_match('/\/sistema/', urlCurrent())) {
    $maintenance_mode = settings()
        ->where_key('maintenance_mode')
        ->first(['value']);

    $is_maintenance_route = preg_match('/\/maintenance/', urlCurrent());

    if ($maintenance_mode && (int)$maintenance_mode->value === 1) {
        if (!$is_maintenance_route) {
            redirect('/maintenance');
        }
    } elseif ($is_maintenance_route) {
        redirect('/');
    }
}

$route_files = array_merge(
    glob(APP_PATH . 'pages/*/*/routes.php'),
    glob(APP_PATH . 'pages/*/routes.php')
);

// Incluindo as rotas do sistema
foreach ($route_files as $route_file) {
    require_once($route_file);
}

Route::get('/download-article-attachment', function () {
    if (!logged_user() && !logged_author()) {
        return response_output('Acesso não permitido!', 503);
    }

    $path = input('path');

    if (!$path) {
        return response_output('Acesso não permitido!', 503);
    }

    if (preg_match('/http.*digitaloceanspaces/i', $path)) {
        return redirect($path);
    }

    $name = input('name', $path);

    $file_full_path = ROOT_PATH . '/storage/articles/' . $path;

    if (!file_exists($file_full_path)) {
        return response_output('O arquivo não existe!', 404);
    }

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_full_path));
    readfile($file_full_path);
    exit();
});

Route::get('/ping', fn () => 'pong');
Route::get('/sistema/ping', fn () => 'pong');

// Route::get('/fix-coupons-93huhodek4wcbh13ikgbg1pxxwjntb0et9je8isykrz7ooiqol1uqpr9iy5mukn4', function () {
//     $articles = DB::query('select 
//         s.id,
//         s.status,
//         s.affiliate_coupon_id,
//         s.collection_id,
//         s.author_id,
//         s.type_id,
//         s.title,
//         s.created_at,
//         s.amount,
//         s.gross_amount
//     from articles s 
//     left join affiliates_coupons_entries e on e.article_id = s.id
//     left join articles_integrations_services p on p.id = s.payment_id 
//     where 
//         s.affiliate_coupon_id is not null
//         and p.service_status = 2
//         and e.id is null');

//     foreach ($articles as $article) {
//         // sendPaymentMail($article);
//         // createCouponEntry(
//         //     get_coupon_data($article->affiliate_coupon_id, $article),
//         //     $article
//         // );
//         // dispatch_on_collection_change($article);
//         $article->coupon = get_coupon_data($article->affiliate_coupon_id, $article);
//     }

//     return response_json($articles);
// });

if (env('DEV_MODE', false)) {
    Route::get('test-send-mail', function () {
        sendMail('marcioantunes.ma@gmail.com', 'OLA TESTE', 'OLA TESTE');

        return 'OK';
    });

    Route::get('test-mail/{mail?}', function ($mail = null) {
        $mails = [
            'article-change-status-payment',
            'article-change-status-production',
            'article-change-status-published',
            'article-change-status-refused',
            'article-created',
            'article-payment-fail',
            'article-payment-success',
            'author-welcome',
            'collection-author-status-failed',
            'collection-author-status-production',
            'collection-organizer-new-article',
            'collection-organizer-status-approved',
            'collection-organizer-status-rejected',
            'collection-organizer-status-submitted',
            'review-created',
            'review-finished'
        ];

        if ($mail) {
            return view('mail.' . $mail, [
                'name' => 'Test User',
                'title' => 'Test Title',
                'store_url' => 'http://test_store_url.com',
                'store_coupon' => 'TestA1B2C3D4',
                'coupon' => 'TestA1B2C3D4',
                'editor_name' => 'Test Author Name',
                'minimum_articles_in_collection' => 5
            ]);
        }

        $body = implode('', array_map(function ($view) {
            return '<p><a href="' . url('test-mail/' . $view) . '">' . $view . '</a></p>';
        }, $mails));

        return '<!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="description" content="EDITORA DIALÉTICA" />
        <meta name="author" content="Phixies" />
        <title>EDITORA DIALÉTICA</title>
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="' . url('public/css/theme.css') . '">
        <link rel="stylesheet" href="' . url('public/css/colors.css') . '">
        </head>
            <body>
            ' . $body . '
            </body>
        </html>';
    });
}
// Route::get('test-review-created-mail', function () {
//     $data = [
//         'title' => 'Meu artigo de testes',
//         'name' => 'Marcio Antunes'
//     ];

//     return view('mail.review-created', $data + ['editor_name' => config('editor_name')]);
// });

$response = Router::run();
if (is_null($response)) {
    $response = isAjax() ? response_json_fail('Page Not Found', 404) : response_html(view('404'), 404);
}
sapi_emit($response);
