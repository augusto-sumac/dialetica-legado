@extend('layouts.sistema')

<?php
$options = [
    'title' => 'Artigos',
    'icon' => 'book',
    'route_adds' => 'sistema/artigos/adicionar',
];
?>
@section('content')
    @include('components.content-header', $options)

    @include('components.datagrid', $datagrid)
@endsection
