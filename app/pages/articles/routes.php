<?php

Route::group([
    'prefix' => 'articles',
    'before' => 'is-logged'
], function () {
    Route::get('/{id:\d+}/invoice', function ($id) {
        try {
            $row = articles()
                ->join(
                    TB_ARTICLES_PAYMENTS . ' as invoice',
                    'invoice.id',
                    '=',
                    TB_ARTICLES . '.invoice_id'
                )
                ->where(TB_ARTICLES . '.id', $id)
                ->first([
                    TB_ARTICLES . '.id',
                    TB_ARTICLES . '.invoice_id',
                    'invoice.service_id'
                ]);

            if (!$row) {
                throw new NotFoundJsonResponseException(
                    response_json([
                        'message' => 'Não existe um registo com o ID informado!',
                    ], 404)
                );
            }
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }

        require_once(ROOT_PATH . 'vendor/PlugNotas/PlugNotas.php');

        $service = (new PlugNotas(config('plug_notas_api_key'), config('plug_notas_sandbox')))->invoices();

        try {
            $file_base_path = '/tmp/' . $row->service_id . '.pdf';
            $file_full_path = $file_base_path;

            $service->download($row->service_id, $file_full_path);

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="Obra-' . $row->id . '-' . $row->service_id . '.pdf"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_full_path));
            readfile($file_full_path);
            exit();
        } catch (\Exception $e) {
            return response_json_fail($e->getMessage());
        }
    });

    Route::get('/{id:\d+}/certificate', function ($id) {
        $article = first_or_fail(
            articles()
                ->join(
                    TB_ARTICLES_PAYMENTS . ' as payment',
                    'payment.id',
                    '=',
                    TB_ARTICLES . '.payment_id'
                )
                ->where(TB_ARTICLES . '.id', $id),
            [
                TB_ARTICLES . '.id',
                TB_ARTICLES . '.title',
                TB_ARTICLES . '.author_id',
                'payment.finished_at as payment_date',
            ]
        );

        $author = find_or_fail(authors(), $article->author_id, ['name']);

        $authors_names = [
            md5(Str::upper($author->name)) => Str::title($author->name)
        ];

        $co_authors = articles_coauthors()
            ->where_article_id($article->id)
            ->where_null('deleted_at')
            ->get(['name']);

        foreach ($co_authors as $co_author) {
            $authors_names[md5(Str::upper($co_author->name))] = Str::title($co_author->name);
        }

        $authors_names = array_values($authors_names);

        $authors_names = array_map(function ($name) {
            return '<strong>' . $name . '</strong>';
        }, $authors_names);

        $last_author = array_pop($authors_names);
        $authors_names = $authors_names ? implode(', ', $authors_names) . ' e ' . $last_author : $last_author;

        $article->authors_names = $authors_names;

        $article->year = substr($article->payment_date, 0, 4);
        $article->month = substr($article->payment_date, 5, 2);
        $article->day = substr($article->payment_date, 8, 2);

        return view('layouts.certificate', (array) $article);
    });
});
