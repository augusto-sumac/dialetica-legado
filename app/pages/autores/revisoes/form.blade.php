@extend('layouts.autores')

@section('content')
    @set('options', ['title' => 'Solicitar Revisão', 'icon' => 'spell-check'])
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Solicitar Revisão'])

    <div class="card">
        <div class="row steps">
            <div class="steps-bar-wrapper col-auto pe-0 border-end border-light" style="width: 250px;">
                <div class="p-4">
                    <ul class="steps-bar">
                        @foreach ($steps as $step_key => $step_item)
                            <li class="{{ $step_key <= $step ? 'active' : '' }}">{{ $step_item['label'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col steps-content-wrapper">
                <div class="id-cobranca" style="{{ $id ? '' : 'display: none;' }}">
                    <div class="p-4 pb-0">
                        <div class="row align-items-center">
                            <div class="col">
                                <h3 class="m-0">
                                    <strong class="text-purple-600">
                                        #{{ str_pad_id($id ? $id : 0) }}
                                    </strong>
                                </h3>
                            </div>
                            <div class="col-auto wrapper-cancel d-none">
                                <button type="button" class="btn btn-danger btn-sm cancel-bill">
                                    <span class="fas fa-times"></span>
                                    <span>Cancelar</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="mb-0">
                </div>

                @foreach ($steps as $step_key => $step_item)
                    <div class="step-header pb-xs-0 px-4 pt-4 {{ $step_key <= $step ? 'active' : '' }}">
                        <div class="step-header-title">
                            {{ $step_item['label'] }}
                        </div>
                    </div>
                    <div class="step pt-xs-0 pt-md-4 px-4 pb-4" style="{{ $step_key === $step ? '' : 'display: none;' }}">
                        @include($step_item['component'], $step_item)

                        @include(AUTHOR_REVIEWS_VIEW_PATH . '.components.form-actions', $step_item)
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .steps {}

        .steps .steps-bar {
            list-style: none;
            font-weight: 700;
            counter-reset: container 0;
            padding-left: 0;
        }

        .steps .steps-bar li {
            margin-left: 40px;
            margin-top: 50px;
            counter-increment: container 1;
        }

        .steps .steps-content-wrapper {
            counter-reset: step_container 0;
        }

        .steps .steps-content-wrapper .step-header {
            display: none;
        }

        .steps .steps-content-wrapper .step-header .step-header-title {
            position: relative;
            margin-left: 40px;
            counter-increment: step_container 1;
            margin-bottom: 20px;
        }

        .steps .steps-content-wrapper .step-header .step-header-title::before {
            content: counter(step_container);
            font-size: 13px;
            line-height: 24px;
            height: 26px;
            width: 26px;
            border-radius: 50%;
            left: -40px;
            top: -2px;
        }

        .steps .steps-bar li,
        .steps .step .step-header,
        .steps .step .step-header .step-header-title {
            font-weight: normal;
            color: #98aac2;
            position: relative;
        }

        .steps .steps-bar li::before,
        .steps .steps-content-wrapper .step-header .step-header-title::before {
            background-color: #91a6c4;
            color: #f7f7f7;
            text-align: center;
            position: absolute;
            border-radius: 50%;
            z-index: 10;
        }

        .steps .steps-bar li::before {
            content: counter(container);
            font-size: 15px;
            line-height: 34px;
            height: 36px;
            width: 36px;
            left: -50px;
            top: -7px;
        }

        .steps .steps-bar li::after {
            content: '';
            position: absolute;
            height: 90px;
            width: 1px;
            background-color: #fff;
            z-index: 1;
            left: -33px;
            top: -70px;
            color: #fff;
        }

        .steps .steps-bar li:first-child {
            margin-top: 1rem;
        }

        .steps .steps-bar li:first-child::after {
            content: '';
            display: none;
        }

        .steps .steps-bar li.active,
        .steps .step-header.active,
        .steps .step-header.active .step-header-title {
            font-weight: bold;
            color: #7c93b3;
        }

        .steps .steps-bar li.active::after {
            border: 1px solid #859ab8;
        }

        .steps .steps-bar li.active::before {
            color: #fff;
            background-color: #6e84a3;
        }

        .steps .steps-bar li.active:last-child {
            color: #2c7be5;
        }

        .steps .steps-bar li.active:last-child:before {
            color: #fff;
            background-color: #2c7be5;
        }

        .status-icon {
            width: 26px;
            height: 26px;
            line-height: 26px;
            border-radius: 100%;
            font-size: 14px;
            display: inline-block;
            text-align: center;
        }

        @media (max-width: 768px) {
            .steps .steps-bar-wrapper {
                display: none;
            }

            .steps .steps-content-wrapper .step-header {
                display: block;
            }
        }

        @media(min-width: 768px) {
            .steps .steps-content-wrapper {
                padding-left: 0;
            }
        }

        .tagin {
            display: none;
        }

        .drop-file {
            border-radius: 5px;
            border: 2px dashed #ddd;
            padding: 3rem;
            opacity: .9;
        }

        .drop-file:hover,
        .drop-file.is-dragover {
            opacity: 1;
            border-color: #ccc;
            background-color: #f7f7f7;
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

        .tags-wrapper .form-group {
            margin: 0;
        }

        .tags-wrapper .form-group+.form-group {
            margin-top: 1rem;
        }

        @media(min-width: 768px) {
            .payment-form .col-form-label {
                max-width: 150px;
            }
        }
    </style>
@endsection

@section('js')
    <script>
        var current_step = {{ $step }};
        var steps = {{ json_encode($steps) }};
        var article_max_file_size = {{ (int) config('article_max_file_size') }};

        function initSteps() {
            $('.steps').find('.steps-bar').find('li').removeClass('active')
            $('.steps').find('.steps-bar').find('li').map((_, el) => {
                if ($(el).index() <= current_step) {
                    $(el).addClass('active')
                }
            });
            toggleStepView();
        }

        function getActiveStep() {
            var step = $('.steps').find('.steps-bar').find('li.active');
            if (step.length) {
                return step;
            }
            return $('.steps').find('.steps-bar').find('li').first();
        }

        function nextStep() {
            getActiveStep().last().next('li').addClass('active');
            toggleStepView();
        }

        function prevStep() {
            getActiveStep().last().removeClass('active');
            toggleStepView();
        }

        function toggleStepView() {
            current_step = getActiveStep().last().index();
            $('.steps').find('.step').hide();
            $('.steps').find('.step').eq(current_step).show();

            $('#article-review-loader').show();

            if (current_step === 2) {
                $.get(baseUrl + '/revisoes/review/' + $('[name="id"]').first().val())
                    .done(function(html) {
                        $('#article-review-content').html(html);
                    })
                    .always(function() {
                        $('#article-review-loader').hide();
                    });
            }

            if (current_step === 3) {
                $.get(baseUrl + '/payments/' + $('[name="id"]').first().val())
                    .done(function(html) {
                        $('#article-payment-content').html(html);

                        setTimeout(function() {
                            $('#article-payment-content select').map(function() {
                                make_select_picker($(this));
                            });

                            $('[name="do"]').mask('##############################');
                            $('[name="nu"]').mask('#### #### #### 9999 999');
                            $('[name="ex"]').mask('##/#9');
                            $('[name="cv"]').mask('#######9');
                        }, 250);
                    })
                    .always(function() {
                        $('#article-payment-loader').hide();
                    });
            }
        }

        $(document).ready(function() {
            initSteps();

            var selected_file = null;

            $(document)
                .on('click', '.goto-payment', function() {
                    let id = $('[name="id"]').first().val()
                    window.location.href = `${baseUrl}/payments/${id}`;
                })
                .on('click', '.update-address', function() {
                    $(this).hide();
                    $('.cancel-update-address').show();
                    $('[name="update_address"]').val(1);
                    $('#form-address').show();
                })
                .on('click', '.cancel-update-address', function() {
                    $(this).hide();
                    $('.update-address').show();
                    $('[name="update_address"]').val(0);
                    $('#form-address').hide();
                })
                .on('click', '.form-confirm', function() {
                    var form = $(this).closest('.step').find('form');
                    var attachment = $('[name="attachment"]');
                    var is_file_form = form.attr('id') === 'article-file';

                    if (
                        is_file_form &&
                        attachment.val() === ''
                    ) {
                        if (!selected_file) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Atenção',
                                text: 'Selecione um arquivo para continuar'
                            });

                            return;
                        }

                        var form_data = new FormData();
                        form_data.append('id', form.find('[name="id"]').val());
                        form_data.append('file', selected_file);

                        var words_count_progress_interval;
                        var words_count_progress_current_progress = 0;
                        var words_count_progress_step = 0.25;

                        var loading = Swal.fire({
                            title: "Aguarde...",
                            html: `
                            <p>O sistema está realizando a contagem de palavras do seu texto.</p>
                            <p>Esse processo pode demorar um pouquinho...</p>
                            <div class="progress mt-5" id="words-count-progress">
                                <div class="progress-bar progress-bar-animated" style="width: 0%"></div>
                            </div>
                            `,
                            allowEscapeKey: false,
                            allowOutsideClick: false,
                            showCancelButton: false,
                            showConfirmButton: false,
                            showCloseButton: false,
                            didOpen: () => {
                                words_count_progress_interval = setInterval(function() {
                                    words_count_progress_current_progress +=
                                        words_count_progress_step;
                                    var progress =
                                        Math.round(
                                            Math.round(
                                                (Math.atan(
                                                    words_count_progress_current_progress
                                                ) / (Math.PI / 2)) *
                                                100 * 1000
                                            ) / 1000
                                        );

                                    $("#words-count-progress .progress-bar").css("width",
                                        progress + "%");

                                    if (progress >= 100) {
                                        clearInterval(words_count_progress_interval);
                                    } else if (progress >= 70) {
                                        words_count_progress_step = 0.1;
                                    }

                                }, 500);
                            },
                            willClose: () => {
                                clearInterval(words_count_progress_interval);
                            },
                        });

                        $.ajax({
                                type: 'POST',
                                url: baseUrl + '/revisoes/arquivo',
                                data: form_data,
                                contentType: false,
                                processData: false,
                                global: false
                            })
                            .done(function(data) {
                                attachment.val(JSON.stringify({
                                    name: data.item.name,
                                    path: data.item.path,
                                    size: data.item.size,
                                }));

                                nextStep();
                            })
                            .fail(function(res) {
                                let text = res?.responseJSON?.message || 'Houve um erro ao enviar o arquivo. Tente novamente!'

                                Swal.fire({
                                    icon: 'error',
                                    title: 'Atenção',
                                    text
                                });

                                attachment.val('');
                            })
                            .always(function() {
                                loading.close();
                            });

                        return;
                    }

                    submitForm({
                        form,
                        onSuccess(data) {
                            $('[name="id"]').val(data.item.id);

                            if (is_file_form) {
                                $('input#select_file').val(null).trigger('change');
                                selected_file = null;
                            }

                            nextStep();
                        },
                        onError: function(data) {
                            if (!data.error_message) return;

                            Swal.fire({
                                icon: 'error',
                                title: 'Ocorrer um erro!',
                                html: [
                                    '<p>Falha ao realizar o pagamento.</p>',
                                    '<p>Retorno do autorizador: <br/><strong class="text-danger">' +
                                    data.error_message + '</strong></p>',
                                    '<p>Tente novamente!</p>'
                                ].join('')
                            });
                        }
                    })
                })
                .on('click', '.form-back', function() {
                    prevStep();
                })
                .on('change', 'select[name="area_id"]', function() {
                    make_subareas_options();
                    make_especialidades_options();
                })
                .on('change', 'select[name="subarea_id"]', function() {
                    make_especialidades_options();
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
                })
                .on('change', '[name="discount_coupon"]', function() {
                    var input = $(this);
                    var token = input.val();
                    var form = $(this).closest('.step').find('form');
                    var id = form.find('[name="id"]').val();

                    input.closest('.form-group').find('.discount-info').remove();

                    $.get(baseUrl + '/revisoes/' + id + '/cupom/' + token)
                        .done(function(data) {
                            form.find('[name="in"]').empty().html(data.installments).data('selectpicker')
                                .refresh();

                            if (data.valid) {
                                let discount_amount = data.discount_percent === 100 ? '100%' : 'R$ ' +
                                    toMoneyBr(data.discount_amount)

                                input.closest('.form-group').append(
                                    ['<div class="discount-info mt-4">',
                                        '<span class="alert alert-success">',
                                        'Você recebeu <strong>',
                                        discount_amount,
                                        '</strong> de desconto!',
                                        '</span>',
                                        '</div>'
                                    ].join('')
                                )

                                if (data.discount_percent === 100) {
                                    $('.payment-installments-group, .payment-card-group').hide();
                                } else {
                                    $('.payment-installments-group, .payment-card-group').show();
                                }

                                setTimeout(function() {
                                    Toast.fire({
                                        icon: 'success',
                                        title: 'Cupom aplicado com sucesso'
                                    });
                                }, 100)
                            } else {
                                if (token.length) {
                                    setTimeout(function() {
                                        Toast.fire({
                                            icon: 'error',
                                            title: data.message ||
                                                'O cupom informado não é válido'
                                        });
                                    }, 100);
                                }

                                $('[name="discount_coupon"]').val('').focus();

                                $('.payment-installments-group, .payment-card-group').show();
                            }
                        })
                        .fail(function(res) {
                            $('.payment-installments-group, .payment-card-group').show();

                            Toast.fire({
                                icon: 'error',
                                title: res?.responseJSON?.message || 'O cupom informado não é válido'
                            });

                            $('[name="discount_coupon"]').val('').focus();
                        });
                });

            function updateSelectedFile(file) {
                if (!file) return;

                var ext = file && file.name && file.name.split('.').reverse()[0];

                if (!['doc', 'docx'].includes(ext)) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Atenção',
                        text: 'Apenas arquivos DOC e DOCX são permitidos'
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

                selected_file = file;

                $('[name="attachment"]').val('')

                $('.display-file-name')
                    .html(
                        '<strong>Arquivo Selecionado:</strong><br/>' + file.name
                    )
                    .attr('class', 'display-file-name text-start mt-3 mb-0');
            }
        });
    </script>
@endsection
