@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Autores',
    'icon' => 'user-graduate',
    'url' => ARTICLES_COLLECTIONS_BASE_URL,
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.form-header', ['title' => 'Autores'])

    <form id="authors" {{ attr_data_id($id) }} action="{{ urlCurrent() }}" class="card tabs" method="POST">

        @include(AUTHORS_VIEW_PATH . '.form-geral')

        @include('components.form-card-buttons', ['url' => $options['url']])

    </form>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            var form = $('form#authors')

            form
                .on('click', '.form-confirm', function(e) {
                    e.preventDefault();
                    e.stopPropagation()

                    submitForm({
                        form: form,
                        onSuccess: function(data) {
                            window.location.href = baseUrl + '/autores/' + data.item.id;
                        }
                    });

                    return false;
                })
        });
    </script>
@endsection
