<?php

require_once(__DIR__ . '/bootstrap.php');

function job()
{
    print_r("\n");

    logg('Find next payment');

    $items = articles_payments()
        ->where_null('service_id')
        ->where(function ($sub) {
            $sub
                ->where_null('service_status')
                ->or_where_in('service_status', ['NA', 'PROCESSANDO']);
        })
        ->where_null('finished_at')
        ->take(5)
        ->get();

    if (!$items) {
        logg('Has no pending payments');
        print_r("\n");
        sleep(10);
        return;
    }

    logg('Found ' . count($items) . ' payments for process');

    $cielo = new Cielo(config('cielo_merchant_id'), config('cielo_merchant_key'), config('cielo_sandbox'));
    $service = $cielo->payments();

    foreach ($items as $item) {
        $started_at = date('Y-m-d H:i:s');

        logg('Create payment for #' . $item->source_id);

        try {
            // Call service
            $payment = $service->createByArticle($item->source_id);
            $payment_id = array_get($payment, 'Payment.PaymentId');
            $status = (int)array_get($payment, 'Payment.Status');

            // Update payment data
            articles()->where_id($item->source_id)->update([
                'payment_id' => $status === 2 ? $item->id : null, // Force nem payment flow
                'status' => $status === 2 ? 32 : 31 // Aprovado | Rejeitado
            ]);

            articles_payments()
                ->where_id($item->id)
                ->update([
                    'service_id' => $payment_id,
                    'service_status' => $status,
                    'service_response_payload' => json_encode($payment)
                ]);

            $article = articles()->find($item->source_id);
            $author = authors()->find($article->author_id);

            // $view = $status === 2 ? 'success' : 'fail';
            // $message = view('mail.article-payment-' . $view, [
            //     'name' => $author->name,
            // ]);

            $view = $status === 2 ? 'd-95822ef0355549a4a5dab83901cf7262' : 'd-35682a76b9554aeba76aa46caccfb385';
            $message = [
                'id' => $view,
                'vars' => [
                    'first_name' => get_first_name($author->name),
                    'titulo_trabalho' => $article->title
                ]
            ];

            add_job('sendMail', [
                'to' => env('DEV_MODE') ? 'marcioantunes.ma@gmail.com' : $author->email,
                'subject' => $status === 2 ? 'Seu pagamento foi confirmado e seu artigo será publicado' : 'Falta só isso para o seu artigo ser publicado...',
                'message' => $message,
            ]);

            logg('OBR ID# ' . $item->source_id . ', #' . $item->id . ', success: ' . $payment_id);
        } catch (\Exception $e) {
            logg('OBR ID# ' . $item->source_id . ', #' . $item->id . ', error: ' . $e->getMessage());

            add_job('sendMail', [
                'to' => 'marcioantunes.ma@gmail.com',
                'subject' => 'Error: ' . $e->getMessage(),
                'message' => $e->getTraceAsString(),
            ]);

            // Reset payment condition
            articles()->where_id($item->source_id)->update(['payment_id' => null]);
        }

        articles_payments()
            ->where_id($item->id)
            ->update([
                'service_request_payload' => null,
                'started_at' => $started_at,
                'finished_at' => date('Y-m-d H:i:s')
            ]);

        sleep(1);
    }
}

$start_running = (int) date('YmdHis');
$loop = 1;
while (true) {
    logg('LOOP ' . $loop++);
    // Force restart after 5 minutes
    if (((int) date('YmdHis') - $start_running) > 430) {
        logg('Force restart after 430 seconds');
        exit(0);
    }
    job();
    sleep(10);

    guaranteeSingleThread(__FILE__);
}
