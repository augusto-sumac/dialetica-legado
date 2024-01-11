<script id="article-status" type="text/x-handlebars-template">
    <div class="form-group">
        <div class="div">
            <strong>STATUS</strong>
            <div class="mt-1">
                <span class="badge bg-${color}}" title="Status">${label}}</span>
            </div>
        </div>

        ${#if prev_or_next }}
        <div class="row justify-content-between align-items-center mt-4">
            <div class="col-auto">
            ${#if prev }}
                <button class="btn btn-sm btn-${prev.color}} btn-alterar-status" data-status="${prev.value}}" title="Retornar para: ${prev.label}}">
                    <span class="fas fa-arrow-left me-1"></span>
                    ${#if prev.btn_label }}
                        ${prev.btn_label}}
                    ${else}}
                        ${prev.label}}
                    ${/if}}
                </button>
            ${/if}}
            </div>
            <div class="col-auto text-end">
            ${#if next }}
                <button class="btn btn-${next.color}} btn-alterar-status" data-status="${next.value}}" title="Avançar para: ${next.label}}">
                    ${#if next.btn_label }}
                        ${next.btn_label}}
                    ${else}}
                        ${next.label}}
                    ${/if}}
                    <span class="fas fa-arrow-right ms-1"></span>
                </button>
            ${/if}}
            </div>
        </div>
        ${/if}}

        ${#if is_cancellable }}
        <div class="mt-5">
            <button class="btn btn-sm btn-danger btn-cancelar-revisao" title="Cancelar Revisão">
                <span class="fas fa-trash me-1"></span> Cancelar Revisão
            </button>
        </div>
        ${/if}}
    </div>
</script>

<script id="article-invoice-status" type="text/x-handlebars-template">
    ${#if can_create_invoice }}
    <div class="card-body py-0 article-invoice-status-wrapper">
        <div class="form-group">
            <strong>NFS-e</strong>
            ${#if generate }}
                <div class="row mt-3 nf-status">
                    <div class="col">
                        <button class="btn btn-sm btn-dark btn-gerar-nf">Gerar NFS-e</button>
                    </div>
                </div>
            ${else}}
                ${#if in_proccess }}
                    <div class="row mt-3 nf-status">
                        <div class="col">
                            <div class="spinner-border spinner-border-sm"></div> Processando ...
                        </div>
                    </div>
                ${else}}
                    <div class="row mt-3 nf-status">
                        <div class="col">
                            <span class="badge bg-${color}}" title="nf_status">${label}}</span>

                            ${#if download }}
                                <div class="mt-3">
                                    <a href="${baseUrl}}/revisoes/${id}}/invoice/pdf" class="btn btn-light btn-sm" target="invoice-pdf">
                                        <span class="fas fa-download"></span> Baixar PDF
                                    </a>
                                </div>
                            ${/if}}

                            ${#if regenerate }}
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-warning btn-gerar-nf">
                                        Gerar NFS-e Novamente
                                    </button>
                                </div>
                            ${/if}}

                            ${#if nf_message }}
                                <div class="mt-3 bg-gray-200 text-gray-600" style="font-size: 11px; border-left: 3px solid #495057; border-radius: 4px; padding: 6px 12px;">
                                    ${nf_message}}
                                </div>
                            ${/if}}

                        </div>
                    </div>
                ${/if}}
            ${/if}}
        </div>
    </div>
    ${/if}}
</script>

<script id="attachment-link" type="text/x-handlebars-template">
    ${#if attachment.path }}
        <a href="{{ url('/') }}download-article-attachment?path=${attachment.path}}" target="_blank">
            Baixe aqui (${attachment.name}})
        </a>
    ${else}}
        <div>Arquivo ainda não anexado!</div>
    ${/if}}
</script>
