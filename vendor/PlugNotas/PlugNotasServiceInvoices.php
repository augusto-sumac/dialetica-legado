<?php

require_once(__DIR__ . '/PlugNotasService.php');

class PlugNotasServiceInvoices extends PlugNotasService
{
    public $service = 'nfse';

    public $request_data = null;

    public $valid_address_street_type = [
        'Alameda',
        'Avenida',
        'Chácara',
        'Colônia',
        'Condomínio',
        'Estância',
        'Estrada',
        'Fazenda',
        'Praça',
        'Prolongamento',
        'Rodovia',
        'Rua',
        'Sítio',
        'Travessa',
        'Vicinal',
        'Eqnp'
    ];

    public function prepareData($data)
    {
        return $data;
    }

    protected function service($service_amount, $article_id = null, $article_title = null, $article_type_id = null)
    {
        $description = ['SERVICO DE PUBLICACAO DE CAPITULO DE LIVRO'];

        $description_by_type = array_get([
            1 => 'SERVICO DE PUBLICACAO DE CAPITULO DE LIVRO',
            2 => 'SERVICO DE PUBLICACAO DE LIVRO',
            3 => 'SERVIÇO DE REVISÃO DE TEXTO',
        ], $article_type_id ?? 1);

        if ($description_by_type) {
            $description[0] = $description_by_type;
        }

        if ($article_id) {
            $description[] = "#{$article_id}";
        }

        if ($article_title) {
            $article_title = Str::upper(Str::slug($article_title, ' '));
            $description[] = "{$article_title}";
        }

        $description = implode(' - ', $description);

        if (toNumber($service_amount) === 0) {
            $description .= "\n\n* * * SERVIÇO NÃO COBRADO - BRINDE * * *";
        }

        return [
            'codigo' => '03158',
            'descricaoLC116' => 'Datilograf, digitação, estenogrf, expdnte, secret, redação, ed. revis, infr estrut adm e congêneres',
            'discriminacao' => $description,
            // 'cnae' => '03158',
            // 'codigoTributacao' => '3550308',
            // 'codigoCidadeIncidencia' => '3550308',
            // 'descricaoCidadeIncidencia' => 'SÃO PAULO',
            'iss' => [
                'aliquota' => 0,
                'tipoTributacao' => 6
            ],
            'retencao' => [
                'pis' => [
                    'aliquota' => 0
                ],
                'cofins' => [
                    'aliquota' => 0
                ],
                'csll' => [
                    'aliquota' => 0
                ]
            ],
            'valor' => [
                'servico' => toNumber($service_amount)
            ]
        ];
    }

    public function create($data)
    {
        $this->wrap($this->http->post('', $data));

        return $this->data();
    }

    public function getArticleIntegrationId($article_id)
    {
        $suffix = $this->http->getSandbox() ? '' : 'P';
        $length = $this->http->getSandbox() ? 11 : 10;
        $integrationId = 'OBR' . $suffix . str_pad_id($article_id, $length);

        $baseQuery = fn () => articles_integrations_services()
            // ->where(DB::raw("json_unquote(json_extract(service_request_payload, '$[0].idIntegracao'))"), $integrationId)
            ->where('type', 'invoice')
            ->where('operation', 'create')
            ->where('source', 'articles')
            ->where('source_id', $article_id)
            ->where_not_null('finished_at')
            ->where_null('deleted_at');

        // Find last item
        $last = $baseQuery()->order_by('id', 'desc')->first();

        if ($last && !in_array($last->service_status, ['NA', 'PROCESSANDO', 'CONCLUIDO'])) {
            $count = $baseQuery()->count('id') + 1;

            $integrationId .= "-{$count}";
        }

        return $integrationId;
    }

    public function createByArticle($article_id)
    {
        $address_service = (new PlugNotas($this->http->getApiKey(), $this->http->getSandbox()))->address();

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

        if (!$address->city_ibge_id) {
            $service_address = (object)$address_service->find(only_numbers($address->zip_code));
            if (!isset($service_address->ibge)) {
                // throw new InvalidArgumentException('O CEP(' . $article->cep . ') informado não é válido', 422);
            }

            $address->city_ibge_id = $service_address->ibge ?? 3550308;
            authors_addresses()->update(['city_ibge_id' => $service_address->ibge ?? 3550308], $article->author_address_id);
        }

        $customer_phone = only_numbers($author->phone);
        $address_street_type = 'Rua';

        $this->request_data = [
            [
                'idIntegracao' => $this->getArticleIntegrationId($article->id),
                'prestador' => [
                    'cpfCnpj' => config('plug_notas_cpf_cnpj'),
                    'email' => config('plug_notas_email'),
                ],
                'tomador' => [
                    'cpfCnpj' => str_pad(only_numbers(mask($author->document, 'cpf_cnpj')), 11, '0', STR_PAD_LEFT),
                    'razaoSocial' => $author->name,
                    'email' => $author->email,
                    'endereco' => [
                        'descricaoCidade' => $address->city,
                        'cep' => only_numbers($address->zip_code),
                        'tipoLogradouro' => $address_street_type,
                        'logradouro' => $address->street,
                        'codigoCidade' => (string) $address->city_ibge_id,
                        'estado' => $address->state,
                        'numero' => $address->number,
                        'bairro' => Str::limit($address->district, 30, ''),
                    ],
                    'telefone' => [
                        'ddd' => substr($customer_phone, 0, 2),
                        'numero' => substr($customer_phone, 2),
                    ],
                ],
                'servico' => $this->service(
                    $article->amount,
                    $article->id,
                    $article->title,
                    $article->type_id,
                )
            ]
        ];

        logg($this->request_data);

        $this->wrap($this->http->post('', $this->request_data));

        return array_get($this->data(), 'documents.0');
    }

    /**
     * Lista invoices by month
     *
     * @param string $yearMonth Y-m
     * @return void
     */
    public function listByMonth(string $yearMonth)
    {
        $month = \DateTime::createFromFormat('Y-m-d', $yearMonth . '-01');

        if (!$month) {
            throw new InvalidArgumentException('Required format is YYYY-MM date string');
        }

        $params = [
            'cpfCnpj' => config('plug_notas_cpf_cnpj'),
            'dataInicial' => $month->format('Y-m-d'),
            'dataFinal' => $month->format('Y-m-t'),
        ];

        $this->wrap($this->http->get('consultar/periodo', $params));

        return $this->data();
    }

    public function statusById($id)
    {
        $this->wrap($this->http->get('consultar/' . $id));

        return array_get($this->data(), 0);
    }

    public function statusByIntegrationId($integration_id)
    {
        $this->wrap($this->http->get('consultar/' . $integration_id . '/' . only_numbers(config('plug_notas_cpf_cnpj'))));

        return array_get($this->data(), 0);
    }

    public function statusByServiceId($service_id)
    {
        $this->wrap($this->http->get('consultar/' . $service_id));

        return array_get($this->data(), 0);
    }

    public function download($id, $fileName)
    {
        return $this->http->curl('/pdf/' . $id)->download($fileName);
    }
}
