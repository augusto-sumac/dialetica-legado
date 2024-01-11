@extend('layouts.autores')

@section('content')
    <div class="content-header">
        <div class="float-end">
            <a href="{{ url('minhas-coletaneas/adicionar') }}" title="Organizar Coletânea" class="btn btn-sm btn-dark">
                <span class="fas fa-plus"></span>
                <span>Organizar Coletânea</span>
            </a>
        </div>
        <h4>
            <span class="fas fa-sitemap me-3"></span>
            Minhas Coletâneas
        </h4>
    </div>

    <hr>

    @if (empty($rows))
        <div class="card">
            <div class="card-body">
                <div class="p-5 fs-1 text-center">
                    <span class="fas fa-frown me-2"></span> Você ainda não possui nenhuma <strong>coletânea</strong>
                    cadastrada
                </div>

                <div class="mt-5 text-center">
                    <a href="{{ url(AUTHOR_COLLECTIONS_BASE_URL . '/adicionar') }}" class="btn btn-secondary">
                        <span class="fas fa-plus me-2"></span> Criar nova coletânea
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="card list-of-collections">
            <div class="card-body p-0">
                @foreach ($rows as $row)
                    <div class="list-of-collections-entry">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        {{ $row->url }}
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-auto">
                                        <small class="fw-bold">Área: {{ $row->nome_area }}</small>
                                    </div>
                                    <div class="col-auto">
                                        <small class="fw-bold">Subárea: {{ $row->nome_sub_area }}</small>
                                    </div>
                                    <div class="col-auto">
                                        <small class="fw-bold">Especialidade:
                                            {{ $row->nome_especialidade }}</small>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-auto">
                                        <span class="badge bg-{{ $row->status_badge['color'] }}">
                                            {{ $row->status_badge['label'] }}
                                        </span>
                                    </div>
                                    @if ($row->status === 'AP')
                                        <div class="col-auto">
                                            <span class="badge bg-dark">Data Limite:
                                                {{ date('d/m/Y', strtotime('+30 days', strtotime($row->created_at))) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto text-center w-100px">
                                <h1 class="m-0 p-0">
                                    {{ $row->articles }}
                                </h1>
                                <small>Artigos</small>
                            </div>
                        </div>

                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@section('css')
    <style>
        .list-of-collections-entry {
            padding: 1.5rem
        }

        .list-of-collections-entry+.list-of-collections-entry {
            border-top: 1px solid #edf2f9;
        }
    </style>
@endsection
