@extend('layouts.autores')

<?php
$options = [
    'title' => 'Meus Artigos',
    'icon' => 'book',
    'route_add' => 'artigos/adicionar',
];
?>
@section('content')
    @if (!$route_collection_id)
        @include('components.content-header', $options)
    @endif

    @include('pages.autores.artigos.components.article-detail')
@endsection

@section('js')
    <script>
        $(document).ready(function() {});
    </script>
@endsection

@section('css')
    <style>
        @media(max-width: 992px) {
            .remove-border-end-on-mobile {
                border-right: 0 none !important;
            }
        }
    </style>
@endsection
