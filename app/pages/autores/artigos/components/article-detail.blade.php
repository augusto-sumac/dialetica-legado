<div class="card">
    <div class="row">
        <div class="col-12 col-lg-7 col-xl-8 border-end remove-border-end-on-mobile">
            <div class="card-body pb-0">
                <div class="form-group">
                    <div class="row align-items-center">
                        <div class="col status-wrapper">
                            <h4 class="m-0">
                                #ID {{ str_pad_id($id) }} {{ $status_badge }}
                            </h4>
                        </div>
                        <div class="col w-80px text-end">
                            @if (!$route_collection_id)
                                <a href="{{ url(AUTHOR_ARTICLES_BASE_URL) }}" class="btn btn-sm btn-secondary">Voltar</a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <strong>Título</strong>
                    <div>
                        {{ $title }}
                    </div>
                </div>

                <div class="form-group">
                    <strong>Resumo</strong>
                    <div>
                        {{ $resume }}
                    </div>
                </div>

                <div class="form-group">
                    <strong>Data Envio</strong>
                    <div>
                        {{ datetimeFromMySql($created_at) }}
                    </div>
                </div>

                @if ($route_collection_id)
                    <div class="form-group">
                        <strong>Coletânea</strong>
                        <p>{{ $collection->name }}</p>
                    </div>
                @endif

                <div class="form-group">
                    <strong>Área</strong>
                    <div>
                        {{ $nome_area }}
                    </div>
                </div>

                <div class="form-group">
                    <strong>Sub-Área</strong>
                    <div>
                        {{ $nome_sub_area }}
                    </div>
                </div>

                <div class="form-group">
                    <strong>Especialidade</strong>
                    <div>
                        {{ $nome_especialidade }}
                    </div>
                </div>

                <div class="form-group mb-lg-4">
                    <strong>Palavras-chave</strong>
                    <div>
                        {{ $tags }}
                    </div>
                </div>

                @if (!$route_collection_id)
                    <hr>
                    <div class="form-group mb-lg-4">
                        <strong>DOI do Artigo</strong>
                        <br>{{ $doi ?? 'Não Informado' }}
                    </div>
                @endif

                @if (isset($collection->id) && !$route_collection_id)
                    <hr>
                    <div class="form-group mb-lg-4">
                        <strong>Coletânea</strong>
                        <p>{{ $collection->name }}</p>
                        <p>
                            <strong>ISBN Livro Físico</strong>
                            <br> {{ $collection->isbn ?? 'Não Informado' }}
                        </p>
                        <p>
                            <strong>ISBN - E-book</strong>
                            <br>{{ $collection->isbn_e_book ?? 'Não Informado' }}
                        </p>
                        <p>
                            <strong>DOI da Coletânea</strong>
                            <br>{{ $collection->doi ?? 'Não Informado' }}
                        </p>
                        <p>
                            <strong>Link para o livro</strong>
                            <br>
                            @if ($collection->book_url)
                                <a href="{{ $collection->book_url }}" target="book_url" title="Link para o livro">
                                    {{ $collection->book_url }}
                                </a>
                            @else
                                Não Informado
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-12 col-lg-5 col-xl-4 ps-lg-0">
            <hr class="d-xs-block d-lg-none">
            <div class="card-body pb-0 pt-xs-0 pt-lg-4">
                <div class="form-group">
                    <strong>Arquivo</strong>
                    <div>
                        <a href="{{ url('/download-article-attachment?path=' . array_get($attachment, 'path')) }}"
                            target="_blank">
                            Baixe aqui ({{ array_get($attachment, 'name', 'Não Informado') }})
                        </a>
                    </div>
                </div>
            </div>

            <hr>

            <div class="card-body py-0">
                <div class="form-group">
                    <strong>AUTOR</strong>
                    <div class="mt-4">
                        <strong class="text-muted">{{ $author->name }}</strong>
                        <br>
                        {{ $author->role }}
                        <br>
                        {{ $author->email }}
                    </div>
                </div>
            </div>

            @if ($co_authors)
                <hr>

                <div class="card-body pt-0 pb-0">
                    <div class="form-group">
                        <strong>CO-AUTORES</strong>

                        @foreach ($co_authors as $co_author)
                            <div class="mt-4">
                                <strong class="text-muted">{{ $co_author->name }}</strong>
                                <br>
                                {{ $co_author->role }}
                                <br>
                                {{ $co_author->email }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($status >= 30)
                <hr>

                <div class="card-body pt-0 pb-0">
                    <div class="form-group">
                        <strong>PAGAMENTO</strong> {{ $payment_status_badge }}

                        <div class="mt-3 fw-bold fs-1">
                            R$ {{ toMoney($amount) }}
                        </div>

                        @if (!$payment_status && !$route_collection_id)
                            <div class="mt-3">
                                <a class="btn btn-success" href="{{ url(AUTHOR_PAYMENTS_BASE_URL . '/' . $id) }}">
                                    Pagar Agora <span class="ms-1 fas fa-arrow-right"></span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                {{ renderPaymentInfo($id, $payment_status) }}
            @endif

            @if ($status >= 32 && !$route_collection_id)
                <hr>

                <div class="card-body pt-0 pb-0">
                    <div class="form-group">
                        <strong>CERTIFICADO</strong>

                        <div class="mt-3">
                            <a class="btn btn-dark"
                                href="{{ url(AUTHOR_ARTICLES_BASE_URL . '/' . $id . '/certificado') }}"
                                target="certificate">
                                <span class="me-1 fas fa-file-pdf"></span> Gerar Certificado
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
