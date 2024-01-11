<?php
$menu_items = (array) arrayToObject([
    [
        'url' => '/',
        'exact' => true,
        'icon' => 'fas fa-tachometer-alt',
        'text' => 'Início',
    ],
    [
        'url' => '/artigos/adicionar',
        'exact' => true,
        'icon' => 'fas fa-plus',
        'text' => 'Publicar Artigo',
    ],
    [
        'url' => '/revisoes/adicionar',
        'exact' => true,
        'icon' => 'fas fa-plus',
        'text' => 'Solicitar Revisão',
    ],
    [
        'url' => '/minhas-coletaneas/adicionar',
        'exact' => true,
        'icon' => 'fas fa-plus',
        'text' => 'Organizar Coletânea',
    ],
    [
        'url' => '/artigos',
        'exact' => false,
        'icon' => 'fas fa-book',
        'text' => 'Meus artigos',
    ],
    [
        'url' => '/revisoes',
        'exact' => false,
        'icon' => 'fas fa-spell-check',
        'text' => 'Minhas Revisões',
    ],
    [
        'url' => '/minhas-coletaneas',
        'exact' => false,
        'icon' => 'fas fa-sitemap',
        'text' => 'Minhas Coletâneas',
    ],
    [
        'url' => '/minhas-comissoes',
        'exact' => false,
        'icon' => 'fas fa-dollar-sign',
        'text' => 'Minhas Comissões',
    ],
    [
        'url' => '/meus-dados',
        'exact' => false,
        'icon' => 'fas fa-user-circle',
        'text' => 'Meus Dados',
    ],
    [
        'url' => '/normas-de-uso',
        'exact' => false,
        'icon' => 'fas fa-atlas',
        'text' => 'Normas de Uso',
    ],
]);

/* 
if (!env('DEV_MODE')) {
    $menu_items = array_filter($menu_items, fn($item) => strpos($item->url, 'revisoes') === false);
}
*/

$user = logged_author();
$base_url = rtrim(url('/'), '/');
$app_title = 'AUTORES - ' . Str::upper(env('APP_NAME'));
$assets_version = APP_VERSION;
$app_class = 'app-authors';
?>

@extend('layouts.app', compact('menu_items', 'app_title', 'assets_version', 'user', 'base_url', 'app_class'))
