<?php

Route::get('/normas-de-uso', ['before' => 'author-auth', function () {
    return view('pages.autores.normas-de-uso.index');
}]);
