<?php

require_once(__DIR__ . '/bootstrap.php');

function job()
{
    print_r("\n");

    logg('Find next invoice');

    $items = articles_invoices()
        ->where(function ($sub) {
            $sub->where_null('service_status')
                ->or_where_in('service_status', ['NA', 'PROCESSANDO']);
        })
        ->where_not_null('service_id')
        ->where_null('deleted_at')
        ->take(5)
        ->get([
            'id',
            'source_id',
            'service_id'
        ]);

    if (!$items) {
        logg('Has no pending invoices status');
        print_r("\n");
        sleep(10);
        return;
    }

    logg('Found ' . count($items) . ' invoices status for process');

    $plugnotas = new PlugNotas(config('plug_notas_api_key'), config('plug_notas_sandbox'));
    $service = $plugnotas->invoices();

    foreach ($items as $item) {
        try {
            updateInvoiceStatusByIntegrationId($service, $item->id, $item->source_id, $item->service_id, $item);
        } catch (\Exception $e) {
            logg('OBR STATUS ID# ' . $item->id . ', error: ' . $e->getMessage());

            add_job('sendMail', [
                'to' => 'marcioantunes.ma@gmail.com',
                'subject' => 'Error: ' . $e->getMessage(),
                'message' => $e->getTraceAsString(),
            ]);
        }

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
