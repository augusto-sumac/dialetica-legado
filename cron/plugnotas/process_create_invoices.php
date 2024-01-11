<?php
require_once(__DIR__ . '/bootstrap.php');

function job()
{
    print_r("\n");

    logg('Find next invoice');

    $items = articles_invoices()
        ->where_null('service_id')
        ->where(function ($sub) {
            $sub->where_null('service_status')
                ->or_where_in('service_status', ['NA', 'PROCESSANDO', 'ERROR', 'ERROR-DUPLICATED']);
        })
        ->where_null('deleted_at')
        ->take(5)
        ->get([
            'id',
            'source_id',
            'service_id',
            'service_request_payload'
        ]);

    if (!$items) {
        logg('Has no pending invoices');
        print_r("\n");
        sleep(10);
        return;
    }

    logg('Found ' . count($items) . ' invoices for process');

    $plugnotas = new PlugNotas(config('plug_notas_api_key'), config('plug_notas_sandbox'));
    $service = $plugnotas->invoices();

    foreach ($items as $item) {
        $item->service_request_payload = secure_json_decode($item->service_request_payload);

        $started_at = date('Y-m-d H:i:s');
        logg('Create invoice for #' . $item->source_id);

        try {
            // Call service
            $invoice = $service->createByArticle($item->source_id);
            $invoice_id = array_get($invoice, 'id');

            // Update invoice data
            DB::table(TB_ARTICLES)->update([
                'invoice_id' => $item->id
            ], $item->source_id);

            articles_invoices()
                ->where_id($item->id)
                ->update([
                    'service_id' => $invoice_id,
                    'service_status' => 'PROCESSANDO',
                    'service_request_payload' => json_encode($service->request_data),
                    'service_response_payload' => json_encode($invoice)
                ]);

            logg('OBR ID# ' . $item->source_id . ', success: ' . $invoice_id);

            updateInvoiceStatusByIntegrationId($service, $item->id, $item->source_id, $invoice_id, $item);
        } catch (\Exception $e) {
            logg('OBR ID# ' . $item->source_id . ', error: ' . $e->getMessage());

            if (preg_match('/existe.*um.*nfse/i', $e->getMessage())) {
                $success = updateInvoiceStatusByIntegrationId($service, $item->id, $item->source_id, null, $item);
                if (!$success) {
                    articles_invoices()
                        ->where_id($item->id)
                        ->update([
                            'service_status' => 'ERROR - DUPLICATED'
                        ]);
                }
            } else {
                articles_invoices()
                    ->where_id($item->id)
                    ->update([
                        'service_status' => 'ERROR'
                    ]);
            }

            add_job('sendMail', [
                'to' => 'marcioantunes.ma@gmail.com',
                'subject' => '#' . $item->id . ' - Error: ' . $e->getMessage(),
                'message' => $e->getTraceAsString(),
            ]);
        }

        articles_invoices()
            ->where_id($item->id)
            ->update([
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
    // if (((int) date('YmdHis') - $start_running) > 430) {
    //     logg('Force restart after 430 seconds');
    //     exit(0);
    // }
    job();
    sleep(10);

    // guaranteeSingleThread(__FILE__);
}
