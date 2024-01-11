@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Autores',
    'icon' => 'user-graduate',
    'route_add' => AUTHORS_BASE_URL . '/adicionar',
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.datagrid', $datagrid)
@endsection
