<script id="payment-form-dialog" type="text/x-handlebars-template">
    <div class="modal fade" id="paymentModalForm" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div>
                    <div class="alert alert-${status_badge.color}} alert-status">
                        ${status_badge.label}}
                    </div>
                </div>

                <div class="modal-body border-0">
                    <div class="form-group">
                        <strong>Data Solicitação</strong>
                        <div>${ created_at }}</div>
                    </div>

                    ${#if is_paid }}

                    <div class="form-group">
                        <strong>Data Pagamento</strong>
                        <div>${ paid_at }}</div>
                    </div>

                    ${/if}}

                    <div class="form-group">
                        <strong>Valor</strong>
                        <div>${ amount }}</div>
                    </div>

                    <div class="form-group">
                        <strong>Beneficiário</strong>
                        <div>${ author_name }}</div>
                    </div>
                    
                    <div class="form-group">
                        <strong>Dados para transferência</strong>
                        <small class="row gx-0">
                            <div class="col-auto w-130px">
                                <span class="text-muted">Tipo chave pix:</span>
                                <br />
                                <span class="text-dark text-capitalize">${ bank_account.account_pix_type }}</span>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted">Chave pix:</span>
                                <br />
                                <span class="text-dark">${ bank_account.account_pix_key }}</span>
                            </div>
                        </small>
                        <small class="row gx-0 mt-2">
                            <div class="col-auto w-130px">
                                <span class="text-muted">CPF/CNPJ/ID:</span>
                                <br />
                                <span class="text-dark">${ bank_account.account_document }}</span>
                            </div>
                            <div class="col-auto">
                                <span class="text-muted">Nome/Razão Social:</span>
                                <br />
                                <span class="text-dark">${ bank_account.account_name }}</span>
                            </div>
                        </small>
                    </div>

                    ${#if is_pending }}

                    <div class="form-group">
                        <strong>Comprovante de Pagamento</strong>
                        <div class="text-center drop-file">
                            <div>Arquivo no formato JPG, PNG ou PDF (Máx 2mb)</div>
                            <label type="button" class="btn btn-dark mt-3 position-relative">
                                <input id="select_file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png">
                                <input type="hidden" name="payment_attachment" value='${ payment_attachment }}'>
                                <span class="fas fa-upload"></span>
                                <span>Selecionar Arquivo</span>
                            </label>
                            ${#if payment_attachment.name }}
                                <div class="display-file-name text-start mt-3 mb-0">
                                    <strong>Arquivo Vinculado:</strong><br />
                                    ${ payment_attachment.name }}
                                </div>
                            ${else}}
                                <div class="display-file-name"></div>
                            ${/if}}
                        </div>
                    </div>

                    ${/if}}

                    ${#if is_paid }}

                    <div class="form-group">
                        <strong>Comprovante de Pagamento</strong>
                        <div>
                            ${#if has_attachment }}
                            <a href="/download-article-attachment?path=${payment_attachment.path}}&name=${payment_attachment.name}}"
                                target="_blank">
                                ${payment_attachment.name}}
                            </a>
                            ${else}}
                            <strong class="text-muted">Nenhum arquivo anexado</strong>
                            ${/if}}
                        </div>
                    </div>
                    ${/if}}

                </div>


                <div class="modal-footer border-0 justify-content-between">
                    <div class="col">
                        <button type="button" class="btn btn-secondary btn-close-modal" data-bs-toggle="modal">
                            ${#if is_pending }}
                                <span class="fas fa-ban"></span> Cancelar
                            ${else}}
                                <span class="fas fa-times"></span> Fechar
                            ${/if}}
                        </button>
                    </div>
                    ${#if is_pending }}
                    <div class="col text-end">
                        <button type="button" class="btn btn-success btn-confirm-payment">
                            <span class="fas fa-check"></span> Confirmar Pagamento
                        </button>
                    </div>
                    ${/if}}
                </div>

            </div>
        </div>
    </div>
</script>