<?php

Route::get('/sistema', ['before' => 'auth', function () {

    $articles = articles()
        ->select([
            DB::raw('sum(case when status >  0 and deleted_at is null then 1 else 0 end) as submitted'),
            DB::raw('sum(case when status = 30 and deleted_at is null then 1 else 0 end) as approved'),
            DB::raw('sum(case when status = 40 and deleted_at is null then 1 else 0 end) as publishing'),
            DB::raw('sum(case when status = 50 and deleted_at is null then 1 else 0 end) as published'),
        ])
        ->first();

    $reviews = reviews()
        ->select([
            DB::raw('sum(case when status >  0 and deleted_at is null then 1 else 0 end) as created'),
            DB::raw('sum(case when (status > 33 and status < 70) and deleted_at is null then 1 else 0 end) as pending'),
            DB::raw('sum(case when status = 70 and deleted_at is null then 1 else 0 end) as finished'),
        ])
        ->first();

    $current_month = date('Y-m');
    $previous_month = date('Y-m', strtotime('-1 month'));

    $authors = authors()
        ->select([
            DB::raw("count(id) as total"),
            DB::raw("sum(case when created_at like '{$previous_month}%' then 1 else 0 end) as previous_month"),
            DB::raw("sum(case when created_at like '{$current_month}%' then 1 else 0 end) as current_month"),
        ])
        ->first();

    return view('pages.sistema.home.index', compact(
        'articles',
        'reviews',
        'authors',
    ));
}]);
