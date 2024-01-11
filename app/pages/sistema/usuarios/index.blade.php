@extend('layouts.sistema')

@section('content')
    @set('options', ['title' => 'Usuários', 'icon' => 'users', 'route_add' => USERS_BASE_URL . '/adicionar'])
    @include('components.content-header', $options)

    @include('components.datagrid', $datagrid)
@endsection
