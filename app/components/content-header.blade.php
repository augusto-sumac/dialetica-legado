<div class="content-header">
    @if (isset($back_url))
        <div class="float-start">
            <a href="{{ $back_url }}" title="Voltar" class="btn btn-link text-dark fs-1 py-0 ps-0 pe-3">
                <span class="fas fa-arrow-left"></span>
            </a>
        </div>
    @endif
    <div class="float-end">
        @if (isset($route_add))
            <a href="{{ url($route_add) }}" title="Novo cadastro" class="btn btn-sm btn-primary">
                <span class="fas fa-plus"></span>
                <span>Adicionar</span>
            </a>
        @endif
    </div>
    <h4>
        @if (isset($icon))
            <span class="fas fa-{{ $icon }} me-3"></span>
        @endif
        {{ $title }}
    </h4>
</div>

<hr>
