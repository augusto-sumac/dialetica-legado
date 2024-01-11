@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Afiliados / Saques',
    'icon' => 'hand-holding-usd',
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.datagrid', $datagrid)
@endsection

@section('css')
    <style>
        .modal-withdraw-detail {
            padding: 0 0 5px;
        }

        .modal-withdraw-detail .swal2-html-container {
            margin: 0;
            padding: 0;
            text-align: inherit;
        }

        .modal-withdraw-detail .swal2-title {
            display: none;
        }

        .modal-withdraw-detail .alert-status {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            margin: 0;
        }

        .modal-withdraw-detail .card-body .form-group:last-child {
            margin: 0;
        }

        #paymentModalForm .alert-status {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            margin: 0;
        }

        #paymentModalForm .form-group:last-child {
            margin: 0;
        }
    </style>
@endsection

@section('js')
    @include(AFFILIATES_VIEW_PATH . '.components.payment-form')

    <script>
        var selected_file = null;
        $(document).ready(function() {
            $(document)
                .on('click', '.btn-show-payment-modal', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    var el = $(this);
                    var data = el.data('row');

                    data.is_paid = data.status === 'FI';
                    data.is_pending = data.status === 'PE';
                    data.has_attachment = data.payment_attachment.path ? true : false;

                    $('#paymentModalForm').remove();


                    var modal = $.tmpl('#payment-form-dialog', data);

                    $('body').append(modal);

                    var modalEl = document.querySelector('#paymentModalForm');
                    var modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    modalInstance.show();

                    var modalClose = function() {
                        modalInstance.hide();
                        selected_file = null;
                    }

                    $(modalEl).on('hidden.bs.modal', function() {
                        modalInstance.dispose();
                    });

                    $(modalEl)
                        .on('click', '.btn-close-modal', modalClose)
                        .on('click', '.btn-confirm-payment', function() {
                            dialogConfirm('Confirma o pagamento do saque?', function() {
                                var url = baseUrl + '/afiliados/saques/confirm-payment';
                                var attachment = $('[name="payment_attachment"]');
                                var form_data = new FormData();
                                form_data.append('id', data.id);
                                if (selected_file) {
                                    form_data.append('file', selected_file);
                                }

                                $.ajax({
                                        url,
                                        type: 'POST',
                                        data: form_data,
                                        contentType: false,
                                        processData: false,
                                    })
                                    .done(function(data) {
                                        attachment.val(JSON.stringify({
                                            name: data.item.name,
                                            path: data.item.path,
                                            size: data.item.size,
                                        }));

                                        modalClose();

                                        $('.btn-reload-datagrid').click();
                                    })
                                    .fail(function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Atenção',
                                            text: 'Houve um erro ao enviar o arquivo. Tente novamente!'
                                        });

                                        attachment.val('');
                                    });
                            });
                        });

                    return false;
                })
                .on('change', 'input#select_file', function(e) {
                    return updateSelectedFile(
                        e.target.files &&
                        e.target.files[0]
                    );
                })
                .on('drag dragstart dragend dragover dragenter dragleave drop', '.drop-file', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                })
                .on('dragover dragenter', '.drop-file', function(e) {
                    $(this).addClass('is-dragover');
                })
                .on('dragleave dragend drop', '.drop-file', function(e) {
                    $(this).addClass('is-dragover');
                })
                .on('drop', '.drop-file', function(e) {
                    return updateSelectedFile(
                        e.originalEvent &&
                        e.originalEvent.dataTransfer &&
                        e.originalEvent.dataTransfer.files[0]
                    );
                });
        });

        function updateSelectedFile(file) {
            if (!file) return;

            var ext = file && file.name && file.name.split('.').reverse()[0];

            if (!['jpg', 'jpeg', 'png', 'pdf'].includes(ext)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Atenção',
                    text: 'Apenas arquivos JPG, PNG e PDF são permitidos'
                });

                return;
            }

            if (file.size > (2 * 1024 * 1024)) {
                Toast.fire({
                    icon: 'error',
                    title: 'Atenção',
                    text: 'O tamanho máximo permitido é 2mb'
                });

                return;
            }

            selected_file = file;

            $('[name="payment_attachment"]').val('')

            $('.display-file-name')
                .html(
                    '<strong>Arquivo Selecionado:</strong><br/>' + file.name
                )
                .attr('class', 'display-file-name text-start mt-3 mb-0');
        }
    </script>
@endsection
