<?php

Route::get('/', ['before' => 'author-auth', function () {
    $articles = articles()
        ->select([
            DB::raw('sum(case when status >  0 and deleted_at is null then 1 else 0 end) as submitted'),
            DB::raw('sum(case when status = 30 and deleted_at is null then 1 else 0 end) as approved'),
            DB::raw('sum(case when status = 40 and deleted_at is null then 1 else 0 end) as publishing'),
            DB::raw('sum(case when status = 50 and deleted_at is null then 1 else 0 end) as published'),
        ])
        ->where_author_id(logged_author()->id)
        ->first();

    $artices_submitted = $articles->submitted ?? 0;
    $artices_approved = $articles->approved ?? 0;
    $artices_publishing = $articles->publishing ?? 0;
    $artices_published = $articles->published ?? 0;

    $reviews = reviews()
        ->select([
            DB::raw('sum(case when status >  0 and deleted_at is null then 1 else 0 end) as created'),
            DB::raw('sum(case when (status > 33 and status < 70) and deleted_at is null then 1 else 0 end) as pending'),
            DB::raw('sum(case when status = 70 and deleted_at is null then 1 else 0 end) as finished'),
        ])
        ->where_author_id(logged_author()->id)
        ->first();

    $reviews_created = $reviews->created ?? 0;
    $reviews_pending = $reviews->pending ?? 0;
    $reviews_finished = $reviews->finished ?? 0;

    return view('pages.autores.home.index', compact(
        'artices_submitted',
        'artices_approved',
        'artices_publishing',
        'artices_published',

        'reviews_created',
        'reviews_pending',
        'reviews_finished',
    ));
}]);
