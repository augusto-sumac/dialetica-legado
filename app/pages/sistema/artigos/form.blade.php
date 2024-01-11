@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Artigos',
    'icon' => 'book',
];
?>
@section('content')
    @include('components.content-header', $options)

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
                                <a href="{{ url('/sistema/artigos') }}" class="btn btn-sm btn-secondary">Voltar</a>
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
                        <strong>Data Submissão</strong>
                        <div>
                            {{ datetimeFromMySql($created_at) }}
                        </div>
                    </div>

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
                </div>

                <div class="card-body pb-0 pt-xs-0 pt-lg-4" id="form-article">
                    <div class="form-group row">
                        <label class="col-form-label pt-0">
                            <strong>Coletânea</strong>
                        </label>
                        <div class="col-12 col-lg-10 col-xl-8">
                            <select class="form-select" name="collection_id" placeholder="Coletânea">
                                {{ select_options_coletaneas_autores($collection_id, false) }}
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label pt-0">
                            <strong>DOI do artigo</strong>
                        </label>
                        <div class="col-6">
                            <input type="text" class="form-control" name="doi" placeholder="DOI do artigo"
                                value="{{ isset($doi) ? $doi : '' }}">
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-success form-confirm" type="button">
                            <span class="fas fa-save"></span> Salvar Alterações
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5 col-xl-4 ps-lg-0">
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

                <div class="card-body py-0">
                    <div class="form-group">
                        <strong>AUTOR</strong>
                        @if ($author)
                            <div class="mt-4">
                                <strong class="text-muted">{{ $author->name }}</strong>
                                <br>
                                {{ $author->role }}
                                <br>
                                {{ $author->email }}
                            </div>


                            @if (!$author_address_id)
                                <div class="mt-4 empty-address-alert">
                                    <div>
                                        <strong class="text-danger">O endereço do autor não foi vinculado.</strong>
                                    </div>
                                    @if ($addresses)
                                        <br>
                                        <small>Selecione uma das opções abaixo</small>

                                        <div class="mt-4">
                                            @foreach ($addresses as $address)
                                                <div class="row align-content-center align-items-center">
                                                    <div class="col">
                                                        <div>
                                                            @if ($address->street)
                                                                <span>{{ $address->street }}, </span>
                                                            @endif
                                                            @if ($address->number)
                                                                <span>{{ $address->number }}, </span>
                                                            @endif
                                                            @if ($address->complement)
                                                                <span>{{ $address->complement }}, </span>
                                                            @endif
                                                            <br>
                                                            @if ($address->zip_code)
                                                                <span>CEP {{ $address->zip_code }} - </span>
                                                            @endif
                                                            @if ($address->district)
                                                                <span>{{ $address->district }} - </span>
                                                            @endif
                                                            @if ($address->city)
                                                                <span>{{ $address->city }} - </span>
                                                            @endif
                                                            @if ($address->state)
                                                                <span>{{ $address->state }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <button class="btn btn-sm btn-light update-article-address"
                                                            data-bs-toggle="tooltip" title="Usar este endereço"
                                                            data-author-id="{{ $author->id }}"
                                                            data-address-id="{{ $address->id }}">
                                                            <span class="fas fa-check"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <br>
                                        O autor não possui endereço cadastrado.
                                        Clique <a href="{{ url('sistema/autores/' . $author->id) }}">aqui</a>
                                        para atualizar o cadastro do autor.
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="mt-4">
                                Artigo não possui autor vinculado!
                            </div>
                        @endif
                    </div>
                </div>

                @if ($co_authors)
                    <div class="card-body py-0">
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

                <div class="card-body py-0 article-status-wrapper"></div>

                <hr>

                <div class="card-body pt-0 pb-0">
                    <div class="form-group">
                        <strong>PAGAMENTO</strong>

                        <div class="mt-3 fw-bold fs-1">
                            R$ {{ toMoney($amount) }}
                        </div>

                        {{ $status_pagamento_badge }}

                        @if ($tid || $nsu)
                            <div class="mt-3">
                                @if ($nsu)
                                    <div>
                                        <strong>NSU</strong>: {{ $nsu }}
                                    </div>
                                @endif

                                @if ($tid)
                                    <div>
                                        <strong>TID</strong>: {{ $tid }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body py-0 article-invoice-status-wrapper"></div>

                @if ($status >= 32)
                    <div class="card-body pt-0 pb-0">
                        <div class="form-group">
                            <strong>CERTIFICADO</strong>

                            <div class="mt-3">
                                <a class="btn btn-dark" href="{{ url(ARTICLES_BASE_URL . '/' . $id . '/certificado') }}"
                                    target="system-certificate">
                                    <span class="me-1 fas fa-file-pdf"></span> Gerar Certificado
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                @if(user_can_delete(true))
                    <div class="card-body pt-0 pb-0">
                        <div class="form-group">
                            <div class="mt-3">
                                <a class="btn btn-danger btn-delete-article" href="{{ url(ARTICLES_BASE_URL . '/' . $id ) }}">
                                    <span class="me-1 fas fa-trash"></span> Deletar Artigo
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@section('js')
    @include(ARTICLES_VIEW_PATH . '.components.article-status')

    <script>
        var sto_update_invoice_status;
        var article_status = {{ json_encode(article_status(null, 1, $collection_author_id)) }};
        var current_status = {{ (int) $status }};
        var article_id = {{ (int) $id }};
        var article_invoice_status = {{ json_encode(article_invoice_status()) }};
        var gerar_nf = {{ $invoice_id ? 'true' : 'false' }};
        var nf_id = '{{ $invoice_id }}';
        var nf_status = '{{ $nf_status }}';
        var nf_message = '{{ $nf_message }}';
        var author_address_id = '{{ $author_address_id }}';

        function update_invoice_status() {
            if (!gerar_nf || !['NA', 'PROCESSANDO'].includes(nf_status)) return;

            clearTimeout(sto_update_invoice_status);

            sto_update_invoice_status = setTimeout(function() {
                $.get({
                        url: baseUrl + '/artigos/' + article_id + '/invoice/status',
                        global: false
                    })
                    .done(function(data) {
                        nf_id = data.nf_id;
                        nf_status = data.nf_status;
                        nf_message = data.nf_message;
                        make_invoice_status(nf_status);
                    });

                update_invoice_status();
            }, 5000);
        }

        function make_invoice_status(status) {
            var new_status = article_invoice_status.find(item => item.value === status);

            if (!new_status) {
                new_status = {
                    color: 'muted',
                    label: 'Aguardando'
                };
            }

            var in_proccess = ['PROCESSANDO', 'NA'].includes(nf_status);

            var article_invoice_status_html = $.tmpl('#article-invoice-status', {
                color: new_status.color,
                label: new_status.label,
                can_create_invoice: parseInt(current_status) >= 32 && author_address_id,
                generate: (nf_id + '').length < 1,
                in_proccess,
                download: ['CANCELADO', 'CONCLUIDO'].includes(nf_status),
                regenerate: !['CONCLUIDO', 'PROCESSANDO', 'NA'].includes(nf_status),
                baseUrl,
                id: article_id,
                nf_message
            });

            $('.article-invoice-status-wrapper').replaceWith(article_invoice_status_html);
        }

        function change_status(status) {
            var new_status = article_status.find(item => parseInt(item.value) === status);

            if (!new_status) {
                return;
            }

            var message = 'Confirma a alteração do status para: <br/><strong class="text-danger">' +
                new_status.label +
                '</strong>?';

            if (status === current_status) {
                return;
            }

            var validation_errors = {};

            if (parseInt(status) === 50) {

                if (
                    !$('[name="collection_id"]').val()
                ) {
                    validation_errors['collection_id'] = ['Este campo é requerido'];
                }

                if (
                    $('[name="doi"]').val().length === 0
                ) {
                    validation_errors['doi'] = ['Este campo é requerido'];
                }
            }

            if (Object.keys(validation_errors).length) {
                Toast.fire({
                    icon: 'error',
                    title: 'Existem campos inválidos!'
                });

                setFormValidationErrors($('#form-article'), validation_errors);

                return;
            }

            dialogConfirm(message, function() {
                $.put(baseUrl + '/artigos/' + article_id + '/change-status', {
                        status
                    })
                    .done(function() {
                        Toast.fire({
                            icon: 'success',
                            text: 'Status alterado com sucesso'
                        });
                        current_status = status;
                        make_status(current_status);
                        make_invoice_status(nf_status);
                    })
                    .fail(function(response) {
                        Toast.fire({
                            icon: 'error',
                            text: response?.responseJSON?.message ||
                                'Falha ao alterar o status! Tente novamente'
                        });
                    });
            });
        }

        function make_status(status) {
            var new_status = article_status.find(item => parseInt(item.value) === status);

            if (!new_status) {
                new_status = {
                    color: 'muted',
                    label: 'Aguardando',
                    value: -5,
                };
            }

            var prev = new_status.prev && article_status.find(item => parseInt(item.value) === new_status.prev);
            var next = new_status.next && article_status.find(item => parseInt(item.value) === new_status.next);

            var article_status_html = $.tmpl('#article-status', {
                color: new_status.color,
                label: new_status.label,
                prev,
                next,
                prev_or_next: prev || next,
                is_cancellable: parseInt(status) !== 9
            });

            $('.article-status-wrapper').html(article_status_html);
        }

        $(document).ready(function() {

            update_invoice_status();
            make_invoice_status(nf_status);
            make_status(current_status);

            $(document)
                .on('click', '.btn-delete-article', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    let url = $(this).attr('href');

                    dialogConfirm('Confirma a exclusão do artigo?', () => {
                        $.delete(url)
                        .done(() => {
                            Swal.fire({
                                icon: 'success',
                                text: 'Artigo excluído com sucesso',
                                confirmButtonText: "Ok"
                            }).then(function (result) {
                                window.location.href = baseUrl + '/artigos';
                            });                            
                        })
                        .fail(error => {
                            let text = error.responseJSON?.message || 'Falha ao excluir artigo!';

                            Toast.fire({
                                icon: 'error',
                                text
                            });
                        });
                    });

                    return false;
                })
                .on('click', '.btn-cancelar-artigo', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    change_status(9);

                    return false;
                })
                .on('click', '.btn-alterar-status', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    change_status($(this).data('status'));

                    return false;
                })
                .on('click', '.btn-gerar-nf', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var btn = $(this);

                    dialogConfirm('Confirma a geração da NFS-e para esta obra?', function() {
                        btn.prop("disabled", true).prepend(
                            '<span class="spinner-border spinner-border-sm me-2"></span>'
                        );

                        $.post(baseUrl + '/artigos/' + article_id + '/invoice')
                            .done(function(data) {
                                Toast.fire({
                                    icon: 'success',
                                    text: 'Emissão da NFS-e iniciada com sucesso'
                                });

                                gerar_nf = true;
                                nf_id = data.id;
                                nf_status = 'PROCESSANDO'

                                $('.nf-status .col').html(
                                    '<div class="spinner-border spinner-border-sm"></div> Processando ...'
                                );

                                btn.remove();

                                update_invoice_status();
                            })
                            .fail(function(response) {
                                Toast.fire({
                                    icon: 'error',
                                    text: response?.responseJSON?.message ||
                                        'Falha ao gerar NFS-e! Tente novamente'
                                });

                                btn.prop("disabled", false).find('.spinner-border').remove();
                            });
                    });
                })
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    submitForm({
                        form: $('#form-article'),
                        url: baseUrl + '/artigos/' + article_id + '/extra',
                        data: {
                            collection_id: $('[name="collection_id"]').val(),
                            doi: $('[name="doi"]').val(),
                        },
                        method: 'POST',
                        onSuccess() {
                            setTimeout(() => {
                                GlobalLoading.show();
                                window.location.reload();
                            }, 10);
                        }
                    });

                    return false;
                })
                .on('click', '.update-article-address', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var author_id = $(this).data('authorId');
                    var author_address_id = $(this).data('addressId');

                    $.post({
                            url: `${baseUrl}/artigos/update-author-address`,
                            data: {
                                author_id,
                                author_address_id
                            }
                        })
                        .done(function() {
                            $('.empty-address-alert').remove();

                            setTimeout(function() {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Endereço vinculado com sucesso'
                                });
                            }, 250);
                        });

                    return false;
                });

        });
    </script>
@endsection

@section('css')
    <style>
        .card-body+.card-body {
            margin-top: 20px !important;
            padding-top: 20px !important;
            border-top: 1px solid #e3ebf6;
        }

        @media(max-width: 992px) {
            .remove-border-end-on-mobile {
                border-right: 0 none !important;
            }

            .remove-border-end-on-mobile .card-body {
                border-bottom: 1px solid #e3ebf6;
                padding-bottom: 20px !important;
            }
        }
    </style>
@endsection
