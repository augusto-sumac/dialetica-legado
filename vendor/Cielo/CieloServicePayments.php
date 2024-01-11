<?php

require_once(__DIR__ . '/CieloService.php');

class CieloServicePayments extends CieloService
{
    public $service = '1/sales';

    public function prepareData($data)
    {
        return $data;
    }

    public function getArticleIntegrationId($article_id)
    {
        $suffix = $this->http->getSandbox() ? '' : 'P';
        $length = $this->http->getSandbox() ? 11 : 10;
        return 'ART' . $suffix . str_pad_id($article_id, $length);
    }

    /**
     * Create Cielo Payment
     *
     * @param int $article_id
     * @param string $type C => CreditCard, B => Bank Billet, P => Pix
     * @return void
     */
    public function createByArticle($article_id, $type = 'C')
    {
        $article = DB::table(TB_ARTICLES)->find($article_id);
        if (!$article) {
            throw new InvalidArgumentException('Não existe artigo com o id(' . $article_id . ') informado', 422);
        }

        $author = authors()->find($article->author_id);
        if (!$author) {
            throw new InvalidArgumentException('A artigo id(' . $article_id . ') é inválido pois não possui autor relacionado', 422);
        }

        $address = authors_addresses()->find($article->author_address_id);
        if (!$address) {
            throw new InvalidArgumentException('A artigo id(' . $article_id . ') é inválido pois não possui endereço relacionado', 422);
        }

        if (!isset($address->zip_code)) {
            throw new InvalidArgumentException('O campo CEP é necessário no cadastro do endereço', 422);
        }

        $payment = articles_payments()->find($article->payment_id);
        $json = array_get(secure_json_decode($payment->service_request_payload), 0);

        $card = (object) json_decode(Crypter::decrypt($json));

        // FIXME
        if (!isset($card->in)) {
            $card->in = 4;
        }

        $integer_amount = only_numbers(toMoney(toNumber($article->amount)));

        $data = [
            'MerchantOrderId' => $this->getArticleIntegrationId($article->id),
            'Customer' => [
                'Name' => $author->name,
                'Email' => $author->email,
                'Address' => [
                    'Street' => $address->street,
                    'Number' => $address->number,
                    'Complement' => $address->complement,
                    'ZipCode' => only_numbers($address->zip_code),
                    'City' => $address->city,
                    'State' => $address->state,
                    'Country' => 'BRA',
                ]
            ],
            'Payment' => [
                'Type' => 'CreditCard',
                'Amount' => $integer_amount,
                'Currency' => 'BRL',
                'Country' => 'BRA',
                'Installments' => $card->in,
                'Interest' => 'ByMerchant',
                'Capture' => true,
                'Authenticate' => false,
                'SoftDescriptor' => 'EDDL' . $article_id,
                'CreditCard' => [
                    'CardNumber' => only_numbers($card->nu),
                    'Holder' => $card->na,
                    'ExpirationDate' => str_replace('/', '/20', $card->ex),
                    'SecurityCode' => $card->cv,
                    'SaveCard' => false,
                    'Brand' => $card->br,
                    'CardOnFile' => [
                        'Usage' => 'Used',
                        'Reason' => 'Unscheduled'
                    ]
                ],
                'IsCryptoCurrencyNegotiation' => false
            ]
        ];

        $this->wrap($this->http->post('', $data));

        return $this->data();
    }
}
