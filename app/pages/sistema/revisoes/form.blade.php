@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Revisões',
    'icon' => 'spell-check',
];
?>
@section('content')
    @include('components.content-header', $options)

    <div class="card" id="review-form">
        <div class="row">
            <div class="col-12 col-lg-7 col-xl-8 pe-lg-0 border-end remove-border-end-on-mobile">
                <div class="card-body pb-0">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <div class="col status-wrapper">
                                <h4 class="m-0">
                                    #ID {{ str_pad_id($id) }} {{ $status_badge }}
                                </h4>
                            </div>
                            <div class="col w-80px text-end">
                                <a href="{{ url('/sistema/revisoes') }}" class="btn btn-sm btn-secondary">Voltar</a>
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
                        <strong>Data Envio</strong>
                        <div>
                            {{ datetimeFromMySql($created_at) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <strong>Data Prev. Entrega</strong>
                        <div>
                            {{ substr(datetimeFromMySql($due_date), 0, 10) }}
                        </div>
                    </div>

                    @if ($status > 60)
                        <div class="form-group">
                            <strong>Data Conclusão Revisão</strong>
                            <div>
                                {{ substr(datetimeFromMySql($review_date), 0, 10) }}
                            </div>
                        </div>
                    @endif

                </div>

                <div class="card-body pb-0 pt-xs-0 pt-lg-4">
                    <div class="form-group">
                        <strong>Arquivo Original</strong>
                        <div>
                            <a href="{{ url('/download-article-attachment?path=' . array_get($attachment, 'path')) }}"
                                target="_blank">
                                Baixe aqui ({{ array_get($attachment, 'name', 'Não Informado') }})
                            </a>
                        </div>
                    </div>

                    <div class="form-group">
                        <strong>Qtde de Palavras</strong>
                        <div>
                            {{ $words_count }}
                        </div>
                    </div>
                </div>

                <div class="card-body pb-0 pt-xs-0 pt-lg-4">
                    <div class="form-group">
                        <strong>Arquivo Prova</strong>
                        <div id="article_proof_attachment"></div>
                        @if ($status < 70)
                            <div class="mt-2 drop-file {{ $status < 33 ? 'd-none' : '' }}">
                                <label type="button" class="btn btn-dark mt-3 position-relative">
                                    <input name="file" type="file" accept=".doc,.docx,.pdf" data-target="proof">
                                    <span class="fas fa-upload"></span>
                                    <span>Anexar Arquivo</span>
                                </label>
                            </div>
                        @endif
                        <input type="hidden" name="proof_attachment">
                    </div>
                </div>

                <div class="card-body pb-0 pt-xs-0 pt-lg-4">
                    <div class="form-group">
                        <strong>Arquivo Final</strong>
                        <div id="article_final_attachment"></div>
                        @if ($status < 70)
                            <div class="mt-2 drop-file {{ $status < 33 ? 'd-none' : '' }}">
                                <label type="button" class="btn btn-dark mt-3 position-relative">
                                    <input name="file" type="file" accept=".doc,.docx,.pdf" data-target="final">
                                    <span class="fas fa-upload"></span>
                                    <span>Anexar Arquivo</span>
                                </label>
                            </div>
                        @endif
                        <input type="hidden" name="final_attachment">
                    </div>
                </div>

                <div class="card-body pb-0 pt-xs-0 pt-lg-4">
                    <div class="form-group row">
                        <label class="col-form-label pt-0">
                            <strong>Observação</strong>
                        </label>
                        @if ($status < 70)
                            <div class="col-12">
                                <textarea rows="5" class="form-control" name="review_comment"
                                    placeholder="Observação para o autor ao finalizar a revisão">{{ isset($review_comment) ? $review_comment : '' }}</textarea>

                                <div class="mt-3">
                                    <button class="btn btn-success form-confirm btn-save-review-comment" type="button">
                                        <span class="fas fa-save"></span> Salvar Observação
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="col-12">
                                {{ $review_comment ? $review_comment : 'Não Informado' }}
                            </div>
                        @endif
                    </div>
                </div>

            </div>

            <div class="col-12 col-lg-5 col-xl-4 ps-lg-0">
                <div class="card-body pb-0">
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
                        @else
                            <div class="mt-4">
                                Revisão não possui autor vinculado!
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body py-0 article-status-wrapper"></div>

                <div class="card-body pt-0 pb-0">
                    <div class="form-group">
                        <strong>PAGAMENTO</strong>

                        <div class="mt-3 fw-bold fs-1">
                            R$ {{ toMoney($amount) }}
                        </div>

                        @if ($status >= 30)
                            {{ $status_pagamento_badge }}
                        @endif

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

                @if(user_can_delete(true))
                    <div class="card-body pt-0 pb-0">
                        <div class="form-group">
                            <div class="mt-3">
                                <a class="btn btn-danger btn-delete-review" href="{{ url(REVIEWS_BASE_URL . '/' . $id ) }}">
                                    <span class="me-1 fas fa-trash"></span> Deletar Revisão
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
    @include(REVIEWS_VIEW_PATH . '.components.article-status')

    <script>
        var sto_update_invoice_status;
        var article_status = {{ json_encode(article_status(null, 3)) }};
        var current_status = {{ (int) $status }};
        var article_id = {{ (int) $id }};
        var article_invoice_status = {{ json_encode(article_invoice_status()) }};
        var gerar_nf = {{ $invoice_id ? 'true' : 'false' }};
        var nf_id = '{{ $invoice_id }}';
        var nf_status = '{{ $nf_status }}';
        var nf_message = '{{ $nf_message }}';
        var author_address_id = '{{ $author_address_id }}';
        var attachments =
            {{ json_encode(['proof' => (object) $proof_attachment, 'final' => (object) $final_attachment]) }};

        var article_max_file_size = {{ (int) config('article_max_file_size') }};

        function update_attachement_links() {
            $('#article_proof_attachment').html(
                    $.tmpl('#attachment-link', {
                        baseUrl,
                        attachment: attachments.proof
                    })
                )
                .parent()
                .find('[name="proof_attachment"]')
                .val(attachments.proof.name);

            $('#article_final_attachment').html(
                    $.tmpl('#attachment-link', {
                        baseUrl,
                        attachment: attachments.final
                    })
                )
                .parent()
                .find('[name="final_attachment"]')
                .val(attachments.final.name);
        }

        function update_invoice_status() {
            if (!gerar_nf || !['NA', 'PROCESSANDO'].includes(nf_status)) return;

            clearTimeout(sto_update_invoice_status);

            sto_update_invoice_status = setTimeout(function() {
                $.get({
                        url: baseUrl + '/revisoes/' + article_id + '/invoice/status',
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
                    label: 'Aguardando/Não permitido'
                };
            }

            var in_proccess = ['PROCESSANDO', 'NA'].includes(nf_status);

            var article_invoice_status_html = $.tmpl('#article-invoice-status', {
                color: new_status.color,
                label: new_status.label,
                can_create_invoice: current_status >= 33 && author_address_id,
                generate: !nf_id && current_status > 60,
                in_proccess,
                download: ['CANCELADO', 'CONCLUIDO'].includes(nf_status),
                regenerate: !['CONCLUIDO', 'PROCESSANDO', 'NA'].includes(nf_status) && current_status > 60,
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

            if (parseInt(status) === 70) {
                var validation_errors = {};
                if (!attachments.proof.name) {
                    validation_errors['proof_attachment'] = ['É necessário anexar o arquivo antes de finalizar'];
                }

                if (!attachments.final.name) {
                    validation_errors['final_attachment'] = ['É necessário anexar o arquivo antes de finalizar'];
                }

                if (
                    $('[name="review_comment"]').val().length === 0
                ) {
                    validation_errors['review_comment'] = ['Este campo é requerido'];
                }

                if (Object.keys(validation_errors).length) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Atenção',
                        text: 'Existem pendências que impedem de continuar!'
                    });

                    setFormValidationErrors($('#review-form'), validation_errors);

                    return;
                }
            }

            dialogConfirm(message, function() {
                $.put(baseUrl + '/revisoes/' + article_id + '/change-status', {
                        status,
                        proof_attachment: $('[name="proof_attachment"]').val(),
                        final_attachment: $('[name="final_attachment"]').val(),
                        review_comment: $('[name="review_comment"]').val(),
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

            $('[name="review_comment"]').closest('.card-body').find(':input, button')[next ? 'show' : 'hide']();
        }

        function updateSelectedFile(el, file) {
            if (!file) return;

            var ext = file && file.name && file.name.split('.').reverse()[0];

            if (!['doc', 'docx', 'pdf'].includes(ext)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Atenção',
                    text: 'Apenas arquivos DOC, DOCX e PDF são permitidos'
                });

                return;
            }

            if (file.size > (article_max_file_size * 1024 * 1024)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Atenção',
                    text: 'O tamanho máximo permitido é ' + article_max_file_size + 'mb'
                });

                return;
            }

            var target = $(el).data('target');
            var form_data = new FormData();
            form_data.append('id', article_id);
            form_data.append('target', target);
            form_data.append('file', file);

            $.ajax({
                    type: 'POST',
                    url: baseUrl + '/revisoes/' + article_id + '/arquivo',
                    data: form_data,
                    contentType: false,
                    processData: false,
                })
                .done(function(data) {
                    attachments[target] = data.item;
                })
                .fail(function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Atenção',
                        text: 'Houve um erro ao enviar o arquivo. Tente novamente!'
                    });
                })
                .always(function() {
                    $(el).val(null);
                    update_attachement_links();
                });
        }

        $(document).ready(function() {

            update_invoice_status();
            make_invoice_status(nf_status);
            make_status(current_status);
            update_attachement_links();

            $(document)
                .on('click', '.btn-delete-review', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    let url = $(this).attr('href');

                    dialogConfirm('Confirma a exclusão da revisão?', () => {
                        $.delete(url)
                        .done(() => {
                            Swal.fire({
                                icon: 'success',
                                text: 'Revisão excluída com sucesso',
                                confirmButtonText: "Ok"
                            }).then(function (result) {
                                window.location.href = baseUrl + '/revisoes';
                            });                            
                        })
                        .fail(error => {
                            let text = error.responseJSON?.message || 'Falha ao excluir revisão!';

                            Toast.fire({
                                icon: 'error',
                                text
                            });
                        });
                    });

                    return false;
                })
                .on('change', '.drop-file input[type="file"]', function(e) {
                    return updateSelectedFile($(this),
                        e.target.files &&
                        e.target.files[0]
                    );
                })
                .on('click', '.btn-cancelar-revisao', function(e) {
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

                        $.post(baseUrl + '/revisoes/' + article_id + '/invoice')
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
                .on('click', '.btn-save-review-comment', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    submitForm({
                        form: $('#review-form'),
                        url: baseUrl + '/revisoes/' + article_id + '/comment',
                        data: {
                            review_comment: $('[name="review_comment"]').val()
                        },
                        method: 'POST'
                    });

                    return false;
                });

        });
    </script>
@endsection

@section('css')
    <style>
        .card-body+.card-body {
            padding-top: 20px !important;
            border-top: 1px solid #e3ebf6;
            border-bottom: 0 none;
        }

        @media(max-width: 992px) {
            .remove-border-end-on-mobile {
                border-right: 0 none !important;
            }

            .col-lg-5 .card-body:first-child {
                border-top: 1px solid #e3ebf6;
            }
        }

        .drop-file label {
            position: relative;
        }

        .drop-file input[type="file"] {
            position: absolute;
            opacity: 0;
            overflow: hidden;
            z-index: -1;
            width: 0.1px;
            height: 0.1px;
            top: 0;
            left: -30000px;
        }
    </style>
@endsection
