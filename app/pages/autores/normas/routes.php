<?php

Route::get('/normas', ['before' => 'author-auth', function () {
    return view('pages.autores.normas.index');
}]);
