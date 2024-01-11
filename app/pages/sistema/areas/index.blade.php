@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Áreas',
    'icon' => 'level-up-alt fa-rotate-90',
    'route_add' => ARTICLES_AREAS_BASE_URL . '/adicionar',
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.datagrid', $datagrid)
@endsection
