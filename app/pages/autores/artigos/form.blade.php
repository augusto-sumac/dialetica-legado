@extend('layouts.autores')

@section('content')
    @set('options', ['title' => 'Publicar Artigo', 'icon' => 'book'])
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Publicar Artigo'])

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

                        @include(AUTHOR_ARTICLES_VIEW_PATH . '.components.form-actions', $step_item)
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

        .tags-wrapper .form-group {
            margin: 0;
        }

        .tags-wrapper .form-group+.form-group {
            margin-top: 1rem;
        }
    </style>
@endsection

<?php
$filterable_subareas = linkable_options_subareas_conhecimentos();
$filterable_especialidades = linkable_options_articles_specialties();
?>

@section('js')
    <script>
        var current_step = {{ $step }};
        var authors = {{ json_encode($co_authors) }};
        var author_roles = {{ json_encode(author_role()) }};
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

            if (current_step === 4) {
                $.get(baseUrl + '/artigos/review/' + $('[name="id"]').first().val())
                    .done(function(html) {
                        $('#article-review-content').html(html);
                    })
                    .always(function() {
                        $('#article-review-loader').hide();
                    });
            }
        }

        var filterable_subareas = {{ json_encode($filterable_subareas) }};
        var filterable_especialidades = {{ json_encode($filterable_especialidades) }};

        function make_subareas_options() {
            var selected_area = [$('select[name="area_id"]').val(), $('select[name="area_id"]').data('value')].map(_ =>
                parseInt(_));
            var items = filterable_subareas.filter(function(item) {
                return selected_area.includes(parseInt(item.area_group_value));
            });

            var html = items.map(function(item) {
                var options = item.items.map(function(option) {
                    return '<option value="' + option.value + '">' + option.label + '</option>';
                });
                return '<optgroup label="' + item.area_group_label + '">' + options.join('') + '</optgroup>';
            });

            if ($('select[name="subarea_id"]').length === 0) {
                return;
            }

            $('select[name="subarea_id"]').html(
                html.join('')
            ).val('').selectpicker('refresh');

            $('select[name="subarea_id"]').val(
                $('select[name="subarea_id"]').data('value')
            ).data("selectpicker").refresh();
        }

        function make_especialidades_options() {
            var selected_area = [$('select[name="area_id"]').val(), $('select[name="area_id"]').data('value')].map(_ =>
                parseInt(_));
            var selected_sub_area = [$('select[name="subarea_id"]').val(), $('select[name="subarea_id"]').data('value')]
                .map(_ => parseInt(_));
            var items = filterable_especialidades
                .filter(function(item) {
                    return selected_area.includes(parseInt(item.area_group_value));
                })
                .filter(function(item) {
                    return selected_sub_area.includes(parseInt(item.subarea_group_value));
                });

            items = items.map(item => {
                let first_item = JSON.parse(JSON.stringify(item.items[0]));
                first_item.label = 'Não se aplica';
                first_item.value = 1;
                first_item.style = 'color: red; font-weigth: bold;';
                item.items.unshift(first_item)
                return item;
            });

            var html = items.map(function(item) {
                var options = item.items.map(({
                    value,
                    label,
                    style
                }) => `<option value="${value}" style="${style ? style : ''}">${label}</option>`);

                return '<optgroup label="' + item.subarea_group_label + '">' + options.join('') + '</optgroup>';
            });

            if ($('select[name="specialty_id"]').length === 0) {
                return;
            }

            $('select[name="specialty_id"]').html(
                html.join('')
            ).val('').selectpicker('refresh');

            $('select[name="specialty_id"]').val(
                $('select[name="specialty_id"]').data('value')
            ).data("selectpicker").refresh();
        }

        function update_authors() {
            $('.co-authors').html(
                authors.map(function(row, key) {
                    return $.tmpl('#div-author-line', {
                        key,
                        name: row.name,
                        email: row.email,
                        role: row.role,
                        role_options: author_roles.map(function(role) {
                            return '<option value="' + role.value + '"' + (role.value === row.role ?
                                ' selected' : '') + '>' + role.label + '</option>';
                        })
                    });
                })
            );

            setTimeout(function() {
                $('.co-authors .co-author select').map(function() {
                    make_select_picker($(this));
                });
            }, 250);
        }

        $(document).ready(function() {
            initSteps();
            make_subareas_options();
            make_especialidades_options();

            var selected_file = null;

            $(document)
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

                        $.ajax({
                                type: 'POST',
                                url: baseUrl + '/artigos/arquivo',
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

                                nextStep();
                            })
                            .fail(function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Atenção',
                                    text: 'Houve um erro ao enviar o arquivo. Tente novamente!'
                                });

                                attachment.val('');
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
                        }
                    })
                })
                .on('click', '.form-back', function() {
                    prevStep();
                })
                .on('change', '[name="collection_id"]', onCollectionChange)
                .on('change', 'select[name="area_id"]', function() {
                    $('select[name="area_id"]').data('value', null);
                    $('select[name="subarea_id"]').data('value', null);
                    $('select[name="specialty_id"]').data('value', null);
                    make_subareas_options();
                    make_especialidades_options();
                    validateCollectionRelations('area_id');
                })
                .on('change', 'select[name="subarea_id"]', function() {
                    $('select[name="subarea_id"]').data('value', null);
                    $('select[name="specialty_id"]').data('value', null);
                    make_especialidades_options();
                    validateCollectionRelations('subarea_id');
                    validateCollectionRelations('specialty_id');
                })
                .on('change', 'select[name="specialty_id"]', function() {
                    $('select[name="specialty_id"]').data('value', null);
                    validateCollectionRelations('specialty_id');
                })
                .on('click', '.add-author', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    authors.push({
                        name: null,
                        email: null,
                        role: null
                    });

                    update_authors();

                    return false;
                })
                .on('click', '.remove-author', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    authors.splice($(this).data('key'), 1);

                    update_authors();

                    return false;
                })
                .on('change', '.co-authors :input', function() {
                    authors[$(this).data('key')][$(this).data('name')] = $(this).val();
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
                .on('click', '.btn-reset-article-collection', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dialogConfirm('Ao desvincular a coletânea isso não poderá ser desfeito! Deseja continuar?',
                        () => {
                            let article_id = $('form#article-collection [name="id"]').val();
                            $.post(`/artigos/${article_id}/reset-collection`)
                                .done(function() {
                                    GlobalLoading.show();

                                    window.location.href = `/artigos/editar/${article_id}/1`;
                                });
                        });
                    return false;
                });

            function onCollectionChange() {
                var el = $('[name="collection_id"]')
                var val = el.val();
                var form_group = el.closest('.form-group');

                form_group
                    .find('.collection-description')
                    .remove().end();

                if (!val) return;

                var data = getSelectedCollectionData();

                if (data.description) {
                    form_group.append(
                        '<small class="collection-description mt-3 text-muted">Descrição: <br/>' +
                        data.description + '</small>');
                }

                var applyValue = function(d, key, time = 0) {
                    setTimeout(function() {
                        $('select[name="' + key + '"]').val(d[key]).trigger('change');
                    }, time);
                }

                applyValue(data, 'area_id', 0);
                applyValue(data, 'subarea_id', 250);
                applyValue(data, 'specialty_id', 500);
            }

            onCollectionChange();

            function getSelectedCollectionData() {
                var el = $('[name="collection_id"]');
                return el.find('option[value="' + el.val() + '"]').data('item') || {};
            }

            function validateCollectionRelations(key) {
                var optionValue = $('select[name="' + key + '"]').val();
                var collectionValue = getSelectedCollectionData()[key];
                if (optionValue && parseInt(optionValue) !== parseInt(collectionValue)) {
                    $('select[name="collection_id"]').val(null).trigger('change');
                }
            }

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
