<div class="form-group mb-0 pt-4 pb-2">
    <div class="col">
        @if (isset($prev) && $prev)
            <button type="button" class="form-back btn btn-outline-dark me-4">
                <span class="fas fa-arrow-left me-2"></span> {{ is_string($prev) ? $prev : 'Anterior' }}
            </button>
        @endif

        @if (isset($next) && $next)
            <button type="button" class="form-confirm btn btn-primary">
                {{ is_string($next) ? $next : 'Próximo' }} <span class="fas fa-arrow-right ms-2"></span>
            </button>
        @endif
    </div>
</div>
