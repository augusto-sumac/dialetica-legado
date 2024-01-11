@extend('layouts.autores')

<?php
$options = [
    'title' => 'Meus Artigos',
    'icon' => 'book',
    'route_add' => AUTHOR_ARTICLES_BASE_URL . '/adicionar',
];
?>

@section('content')
    @include('components.content-header', $options)

    @forelse ($rows as $row)
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        @if ($row->status === 0)
                            <a href="{{ url(AUTHOR_ARTICLES_BASE_URL . '/editar/' . $row->id) }}" data-bs-toggle="tooltip"
                                title="Finalizar envio do artigo">
                                {{ $row->title }}
                            </a>
                        @else
                            <a href="{{ url(AUTHOR_ARTICLES_BASE_URL . '/' . $row->id) }}" data-bs-toggle="tooltip"
                                title="Detalhes do artigo">
                                {{ $row->title }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-6 col-md-auto">
                        <small class="text-muted">Área</small>
                        <div> {{ $row->nome_area }}</div>
                    </div>
                    <div class="col-6 col-md-auto">
                        <small class="text-muted">Data Envio</small>
                        <div> {{ $row->created_at }}</div>
                    </div>

                    <div class="col-6 col-md-auto mt-3 mt-md-0">
                        <small class="text-muted">Status</small>
                        <div> {{ $row->status_badge }}</div>
                    </div>
                    <div class="col-6 col-md-auto mt-3 mt-md-0">
                        <small class="text-muted">Status Pagto</small>
                        <div>
                            {{ $row->payment_status_badge }}
                            @if ($row->status >= 30 && !$row->payment_status)
                                <a class="btn btn-success" href="{{ url(AUTHOR_PAYMENTS_BASE_URL . '/' . $row->id) }}">
                                    Pagar Agora <span class="ms-1 fas fa-arrow-right"></span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body">
                <div class="p-5 fs-1 text-center">
                    <span class="fas fa-frown me-2"></span> Você ainda não possui nenhum artigo cadastrado
                </div>

                <div class="mt-5 text-center">
                    <a href="{{ url(AUTHOR_ARTICLES_BASE_URL . '/adicionar') }}" class="btn btn-secondary">
                        <span class="fas fa-upload me-2"></span> Publicar Artigo
                    </a>
                </div>
            </div>
        </div>
    @endforelse
@endsection
