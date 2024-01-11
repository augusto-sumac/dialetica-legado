# Documentação básica

Este "pacote" usa elementos de diversas origens como:

-   DotEnv portado do projeto https://github.com/devcoder-xyz/php-dotenv
-   Adaptados do laravel V3.0 https://laravel3.veliovgroup.com/docs
    -   Sistema de rotas e filtros
    -   Blade Templates
    -   Cookie
    -   Crypter
    -   Database (Sem models)
    -   File
    -   Input
    -   Str
    -   Validator
    -   helpers
-   Adaptado do projeto Siler https://siler.leocavalcante.dev
    -   request
    -   response
-   Outros
    -   PHPMailer
    -   Curl adaptado do projeto https://github.com/ixudra/curl
-   Próprios
    -   InsertOrUpdateMany

Ponto importante, não usa namespaces! Use o bom e velho "include/require"

# Ciclo de vida

Todas as requisições batem no index.php na raiz.

O index inclui o bootstrap.php responsável por carregar as dependências do projeto como helpers, views, db, etc.

O sistema de rotas processa a requisição e ao final a função sapi_emit encerra o processo.

# Rotas e controllers

Este pacote não usa classes para controllers, apenas funções por questões de compatibilidade com servidores e, principalmente, devs roots!

## Definindo um rota

Por conveniência, o index busca todos os arquivos "routes.php" contidos na pasta "app/pages" logo, para uma feature "usuarios" basta criar a pasta usuarios dentro de pages e nesta criar um arquivo chamado "routes.php"

```php
<?php
# ./app/pages/usuarios/routes.php

# Crud básico
Route::get('/usuarios', function() {
    $users = DB::table('users')->get();

    return view('pages.usuarios.index', compact('users'));
});

Route::get('/usuarios/cadastrar', function() {
    return view('pages.usuarios.form');
});

Route::get('/usuarios/{id}/editar', function($id) {
    $user = DB::table('users')->find($id);
    return view('pages.usuarios.form', compact('user'));
});

Route::post('/usuarios/{id?}', function($user_id = null) {
    // Em caso de falha, o bootstrap já captura e interrompe o processo
    validate([
        'name' => 'required|max:150',
        'email' => 'required|email'
    ]);

    $user_data = array_only(input(), ['name', 'email']);

    if ($user_id) {
        DB::table('users')->where('id', $id)->update($user_data);
    } else {
        $user_id = DB::table('users')->insert_get_id($user_data);
    }

    $user_data['id'] = $user_id;

    return response_json([
        'user' => $user_data
    ]);
});

Route::delete('/usuarios/{id}', function($id) {
    DB::table('users')->delete($id);
    return response_json();
});
```

# Dados enviados via GET, POST, JSON

A função **input()** retorna os dados do request

```php
# Pegando um único campo
$name = input('name');

# Pegando um array de campos
$user = array_only(input(), ['name', 'email']);

# Pegando tudo do request
$all = input();
```
