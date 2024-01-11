<?php
require_once(rtrim(__DIR__, DS) . '/curl.php');

class CieloPayment
{
    const PAYMENT_TYPE_CREDIT = 'CreditCard';
    const PAYMENT_TYPE_BILLET = 'Boleto';
    const PAYMENT_TYPE_PIX    = 'Pix';

    const CARD_BRAND_VISA = 'Visa';
    const CARD_BRAND_MASTERCARD = 'Master';
    const CARD_BRAND_AMEX = 'Amex';
    const CARD_BRAND_ELO = 'Elo';
    const CARD_BRAND_AURA = 'Aura';
    const CARD_BRAND_JCB = 'JCB';
    const CARD_BRAND_DINERS = 'Diners';
    const CARD_BRAND_DISCOVER = 'Discover';
    const CARD_BRAND_HIPERCARD = 'Hipercard';

    private $merchantId;

    private $merchantKey;

    private $apiUrl;
    private $apiQueryUrl;

    public function __construct(string $merchantId, string $merchantKey, $sandbox = false)
    {
        $this->merchantId = $merchantId;

        $this->merchantKey = $merchantKey;

        $sandbox = $sandbox ? 'sandbox' : '';

        $this->apiUrl = 'https://api' . $sandbox . '.cieloecommerce.cielo.com.br';

        $this->apiQueryUrl = 'https://apiquery' . $sandbox . '.cieloecommerce.cielo.com.br';
    }

    private function headers()
    {
        return [
            'MerchantId' => $this->merchantId,
            'MerchantKey' => $this->merchantKey
        ];
    }

    private function createCieloSale(object $article, $type = 'C', array $cardData = []): array
    {
        $type = array_get([
            'P' => CieloPayment::PAYMENT_TYPE_PIX,
            'C' => CieloPayment::PAYMENT_TYPE_CREDIT,
            'B' => CieloPayment::PAYMENT_TYPE_BILLET,
        ], $type);

        $author = authors()->find($article->author_id);

        $amount = only_numbers(toMoney(toNumber($article->amount)));
        $installments = array_get($cardData, 'in', 1);

        $customer = [
            'Name' => $author->name,
            'Email' => $author->email,
            'Identity' => $author->document,
            'IdentityType' => strlen($author->document) > 11 ? 'CNPJ' : 'CPF'
        ];

        $isPix    = $type === CieloPayment::PAYMENT_TYPE_PIX;
        $isBillet = $type === CieloPayment::PAYMENT_TYPE_BILLET;
        $isCredit = $type === CieloPayment::PAYMENT_TYPE_CREDIT;

        if ($isBillet) {
            $address = authors_addresses()->find($article->author_address_id);

            $customer['Address'] = [
                'ZipCode' => $address?->zip_code,
                'State' => $address?->state,
                'City' => $address?->city,
                'District' => $address?->district,
                'Street' => $address?->street,
                'Number' => $address?->number,
                'Country' => $address?->country ?? 'BRA',
            ];
        }

        $payment = [
            'Partner' => 'BIT',
            'Type' => $type,
            'Amount' => $amount,
        ];

        if ($isBillet) {
            $settings    = settings()->lists('value', 'key');
            $days        = array_get($settings, 'bank_biller_due_days', 5);
            $dueDate     = date('Y-m-d', strtotime('+' . $days . ' days'));
            $description = array_get([
                1 => 'SERVICO DE PUBLICACAO DE CAPITULO DE LIVRO',
                2 => 'SERVICO DE PUBLICACAO DE LIVRO',
                3 => 'SERVIÇO DE REVISÃO DE TEXTO',
            ], $article->type_id ?? 1);

            $description .= "\n{$article->title}";

            $payment['Address'] = 'Avenida Brigadeiro Faria Lima';
            $payment['Assignor'] = 'EDITORA DIALÉTICA LTDA';
            $payment['Demonstrative'] = $description;
            $payment['ExpirationDate'] = $dueDate;
            $payment['Identification'] = '32431939000105';
            $payment['Instructions'] = 'Não Receber Após o Vencimento';
        }

        if ($isCredit) {
            $securityCode   = array_get($cardData, 'cv', '000');
            $brand          = array_get($cardData, 'br', CieloPayment::CARD_BRAND_VISA);
            $expirationDate = str_replace('/', '/20', array_get($cardData, 'ex', '01/22'));
            $cardNumber     = only_numbers(array_get($cardData, 'nu', 1));
            $holder         = array_get($cardData, 'na', $author->name);

            $payment['Capture'] = true;
            $payment['Installments'] = $installments;

            $payment['CreditCard'] = [
                'CardNumber' => $cardNumber,
                'Holder' => $holder,
                'ExpirationDate' => $expirationDate,
                'SecurityCode' => $securityCode,
                'SaveCard' => false,
                'Brand' => $brand,
                'CardOnFile' => [
                    'Usage' => 'Used',
                    'Reason' => 'Unscheduled'
                ]
            ];
        }

        $sale = [
            'MerchantOrderId' => $this->getArticleIntegrationId($article),
            'Customer' => $customer,
            'Payment' => $payment
        ];

        return $sale;
    }

    private function getArticleIntegrationId(object $article)
    {
        $suffix = config('cielo_sandbox', false) ? '' : 'P';
        $length = config('cielo_sandbox', false) ? 11 : 10;
        return 'ART' . $suffix . str_pad_id($article->id, $length);
    }

    protected function readResponse($response)
    {
        if (in_array($response->status, [200, 201])) {
            return $response->content;
        }

        if ($response->status === 400) {
            $code = array_get($response->content, 0)?->Code;
            $message = array_get($response->content, 0)?->Message;

            throw new \Exception(
                "Cielo Request Error: {$code} -> {$message}",
                $response->status
            );
        }

        throw new \Exception('Resource not found', $response->status);
    }

    public function create(object $article, $type = CieloPayment::PAYMENT_TYPE_CREDIT, array $cardData = [])
    {
        $saleData = $this->createCieloSale($article, $type, $cardData);

        $response = Curl::to($this->apiUrl . '/1/sales/')
            ->withHeaders($this->headers())
            ->withData($saleData)
            ->asJson()
            ->returnResponseObject()
            ->post();

        return $this->readResponse($response);
    }

    public function cancel(string $paymentId, float $amount)
    {
        $amount = only_numbers(toNumber(toMoney($amount)));

        $response = Curl::to($this->apiUrl . '/1/sales/' . $paymentId . '?void=amount=' . $amount)
            ->withHeaders($this->headers())
            ->asJson()
            ->returnResponseObject()
            ->put();

        return $this->readResponse($response);
    }

    public function consult(string $paymentId)
    {
        $response = Curl::to($this->apiQueryUrl . '/1/sales/' . $paymentId)
            ->withHeaders($this->headers())
            ->asJson()
            ->returnResponseObject()
            ->get();

        return $this->readResponse($response);
    }
}
