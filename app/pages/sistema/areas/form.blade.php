@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Áreas',
    'icon' => 'level-up-alt fa-rotate-90',
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Áreas'])

    <form id="areas-conhecimento" {{ attr_data_id($id) }} action="{{ urlCurrent() }}" class="card tabs"
        method="POST">
        <div class="card-body">

            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Área</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" name="name" placeholder="Área"
                        value="{{ isset($name) ? $name : '' }}">
                </div>
            </div>

        </div>

        @include('components.form-card-buttons', ['url' => ARTICLES_AREAS_BASE_URL])

    </form>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            var form = $('form#areas-conhecimento')

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation()

                    submitForm({
                        form: form,
                        onSuccess: function(data) {
                            form
                                .attr('action', baseUrl + '/areas/' + data.item.id + '/editar')
                                .data('id', data.item.id);

                            $(".form-header").text('#' + (data.item.id + '').padStart(5, '0'));

                            $('.add-new').show();
                        }
                    });

                    return false;
                })
        });
    </script>
@endsection
