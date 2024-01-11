<?php

function updateInvoiceStatusByIntegrationId(PlugNotasServiceInvoices $service, $invoice_id, $article_id, $service_id, $item)
{
    logg('Try update invoice status #' . $article_id);

    try {
        $integration_id = array_get($item || [], '0.idIntegracao');
        if (!$integration_id) {
            $integration_id = $service->getArticleIntegrationId($article_id);
        }

        // logg(json_encode(compact('integration_id', 'invoice_id', 'article_id', 'service_id', 'item')));

        $response = (object) $service->statusByIntegrationId($integration_id);

        if ($response) {
            DB::table(TB_ARTICLES)->update([
                'invoice_id' => $invoice_id
            ], $article_id);

            articles_invoices()->where_id($invoice_id)->update([
                'service_id' => $response->id,
                'service_status' => $response->situacao,
                'service_response_payload' => json_encode($response)
            ]);

            logg('Updated invoice status #' . $article_id . ' to #' . $response->id . ' - ' . $response->situacao);

            return true;
        }

        if (!$service_id) return false;

        $response = (object) $service->statusByServiceId($service_id);

        if ($response) {
            DB::table(TB_ARTICLES)->update([
                'invoice_id' => $invoice_id
            ], $article_id);

            articles_invoices()->where_id($invoice_id)->update([
                'service_id' => $response->id,
                'service_status' => $response->situacao,
                'service_response_payload' => json_encode($response)
            ]);

            logg('Updated invoice status #' . $article_id . ' to #' . $response->id . ' - ' . $response->situacao);

            return true;
        }

        logg('Not found invoice status #' . $article_id);
    } catch (\Exception $e) {
        logg($e->getMessage());
        return false;
    }
}
