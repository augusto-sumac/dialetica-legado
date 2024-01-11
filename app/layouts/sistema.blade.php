<?php
$menu_items = (array) arrayToObject([
    [
        'url' => '/sistema',
        'exact' => true,
        'icon' => 'fas fa-tachometer-alt',
        'text' => 'Início',
    ],
    [
        'url' => '/sistema/autores',
        'exact' => false,
        'icon' => 'fas fa-user-graduate',
        'text' => 'Autores',
    ],
    [
        'url' => '/sistema/artigos',
        'exact' => false,
        'icon' => 'fas fa-book',
        'text' => 'Artigos',
    ],
    [
        'url' => '/sistema/coletaneas',
        'exact' => false,
        'icon' => 'fas fa-level-up-alt fa-rotate-90 ms-4',
        'text' => 'Coletâneas',
    ],
    [
        'url' => '/sistema/areas',
        'exact' => false,
        'icon' => 'fas fa-level-up-alt fa-rotate-90 ms-4',
        'text' => 'Áreas',
    ],
    [
        'url' => '/sistema/subareas',
        'exact' => false,
        'icon' => 'fas fa-level-up-alt fa-rotate-90 ms-4',
        'text' => 'Subáreas',
    ],
    [
        'url' => '/sistema/especialidades',
        'exact' => false,
        'icon' => 'fas fa-level-up-alt fa-rotate-90 ms-4',
        'text' => 'Especialidades',
    ],
    [
        'url' => '/sistema/revisoes',
        'exact' => false,
        'icon' => 'fas fa-spell-check',
        'text' => 'Revisões',
    ],
    [
        'url' => urlCurrent() . '#',
        'exact' => false,
        'icon' => 'fas fa-sitemap',
        'text' => 'Afiliados',
    ],
    [
        'url' => '/sistema/afiliados/cupons',
        'exact' => false,
        'icon' => 'fas fa-address-card ms-4',
        'text' => 'Cupons',
    ],
    [
        'url' => '/sistema/afiliados/saques',
        'exact' => false,
        'icon' => 'fas fa-hand-holding-usd ms-4',
        'text' => 'Saques',
    ],
    [
        'url' => '/sistema/configuracoes',
        'exact' => false,
        'icon' => 'fas fa-cogs',
        'text' => 'Configurações',
    ],
    [
        'url' => '/sistema/usuarios',
        'exact' => false,
        'icon' => 'fas fa-users',
        'text' => 'Usuários',
    ],
]);

$user = logged_user();
$base_url = rtrim(url('/sistema'), '/');
$app_title = 'SISTEMA - ' . Str::upper(env('APP_NAME'));
$assets_version = APP_VERSION;
?>

@extend('layouts.app', compact('menu_items', 'app_title', 'assets_version', 'user', 'base_url'))
