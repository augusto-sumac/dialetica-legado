<div class="modal fade" id="modal-coupons-form" v-scope v-cloak tabindex="-1" @vue:mounted="onMounted" @vue:unmounted="onUnmounted">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    ${ item.id ? 'Editar Cupom' : 'Novo Cupom' }}
                </h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal" @click="close"></button>
            </div>

            <div class="modal-body border-0">

                <div class="form-group" :class="{'text-muted': item.uses > 0}">
                    <div>
                        <label class="form-label">
                            CPF/CNPJ/ID Afiliado
                            <a href="javascript:void(0)" class="text-danger" @click="resetUser" v-if="item.uses === 0">
                                [ Desvincular ]
                            </a>
                        </label>
                        <input type="text" v-model="item.document" name="document" class="form-control cpf_cnpj" placeholder="CPF/CNPJ/ID Afiliado" @change="findAffiliate" :disabled="item.uses > 0" :readonly="item.uses > 0">
                    </div>

                    <div v-show="item.author_name || item.uses" class="mt-2">
                        <strong>Afiliado: ${ item.author_name || 'Não Vinculado' }}</strong>
                    </div>
                    <small v-if="item.uses > 0">Não é possível editar pois o cupom já foi utilizado ${item.uses}}x</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Cupom/Código</label>
                    <input type="text" v-model="item.token" name="token" class="form-control" placeholder="Cupom">
                    <small class="text-muted">
                        Utilize apenas: <br />
                        - Letras(sem caracteres latinos[ç, ã, é, etc]) <br />
                        - Números 0 a 9 <br />
                        - E os caracteres $ (cifrão) # (cerquilha) _ (subtraço/underline) e - (hífen)
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Aplicação</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_articles" value="ARTICLES" v-model="item.type" @change="onTypeChange">
                            <label class="form-check-label" for="type_articles">Artigos</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_reviews" value="REVIEWS" v-model="item.type" @change="onTypeChange">
                            <label class="form-check-label" for="type_reviews">Revisoes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="type" id="type_all" value="ALL" v-model="item.type" @change="onTypeChange">
                            <label class="form-check-label" for="type_all">Livre</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Tipo de Desconto/Comissão</label>
                    <div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="value_rule" id="value_rule_settings" value="settings" v-model="value_rule" @change="onValueRuleChange">
                            <label class="form-check-label" for="value_rule_settings">Via Configuração</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="value_rule" id="value_rule_manual" value="manual" v-model="value_rule" @change="onValueRuleChange">
                            <label class="form-check-label" for="value_rule_manual">Manual</label>
                        </div>
                    </div>
                </div>

                <div v-show="value_rule !== 'settings'">

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">Tipo de Desconto</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="discount_rule" id="discount_rule_percent" value="percent" v-model="item.discount_rule" @change="onChangeRule">
                                        <label class="form-check-label" for="discount_rule_percent">%</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="discount_rule" id="discount_rule_fixed" value="fixed" v-model="item.discount_rule" @change="onChangeRule">
                                        <label class="form-check-label" for="discount_rule_fixed">Valor</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group" v-show="item.discount_rule !== 'settings'">
                                <label class="form-label">Valor Desconto</label>
                                <input type="text" v-model="item.discount_value" name="discount_value" class="form-control" placeholder="Valor Desconto" style="max-width: 250px;">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">Tipo de Comissão</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="affiliate_rule" id="affiliate_rule_percent" value="percent" v-model="item.affiliate_rule" @change="onChangeRule">
                                        <label class="form-check-label" for="affiliate_rule_percent">%</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="affiliate_rule" id="affiliate_rule_fixed" value="fixed" v-model="item.affiliate_rule" @change="onChangeRule">
                                        <label class="form-check-label" for="affiliate_rule_fixed">Valor</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col">
                            <div class="form-group" v-show="item.affiliate_rule !== 'settings'">
                                <label class="form-label">Valor Comissão</label>
                                <input type="text" v-model="item.affiliate_value" name="affiliate_value" class="form-control" placeholder="Valor Comissão" style="max-width: 250px;">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Data Validade Inicial</label>
                            <input type="text" v-model="item.start_at_date" name="start_at_date" class="form-control campo-data" placeholder="__/__/____">
                            <small class="text-muted">Vazio: Imediato</small>
                        </div>
                        <div class="col">
                            <label class="form-label">Hora Validade Inicial</label>
                            <input type="text" v-model="item.start_at_time" name="start_at_time" class="form-control campo-hora" placeholder="__:__">
                            <small class="text-muted">Vazio: Imediato</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Data Validade Final</label>
                            <input type="text" v-model="item.expires_at_date" name="expires_at_date" class="form-control campo-data" placeholder="__/__/____">
                            <small class="text-muted">Vazio: Não Expira</small>
                        </div>
                        <div class="col">
                            <label class="form-label">Hora Validade Final</label>
                            <input type="text" v-model="item.expires_at_time" name="expires_at_time" class="form-control campo-hora" placeholder="__:__">
                            <small class="text-muted">Vazio: Não Expira</small>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0">
                    <div class="row">
                        <div class="col">
                            <label class="form-label">Limite Uso Geral</label>
                            <input type="text" v-model="item.max_uses" name="max_uses" class="form-control" placeholder="0">
                            <small class="text-muted">Zero: Ilimitado</small>
                        </div>
                        <div class="col">
                            <label class="form-label">Limite Uso Por Usuário</label>
                            <input type="text" v-model="item.max_uses_user" name="max_uses_user" class="form-control" placeholder="0">
                            <small class="text-muted">Zero: Ilimitado</small>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer border-0 justify-content-between">
                <div class="col">
                    <button type="button" class="btn btn-secondary" @click="close">
                        <span class="fas fa-ban"></span> Cancelar
                    </button>
                </div>
                <div class="col text-end">
                    <button type="button" class="btn btn-success" @click="save">
                        <span class="fas fa-spinner fa-spin" v-if="saving"></span>
                        <span class="fas fa-check" v-else></span>
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import {
        createApp
    } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

    const Modal = bootstrap.Modal;

    const sortByKeys = (source) =>
        Object.keys(source)
        .sort()
        .reduce((target, key) => {
            target[key] = source[key];
            return target;
        }, {});

    const filterEmpty = (source) =>
        Object.keys(source)
        .reduce((target, key) => {
            if (source[key]) target[key] = source[key];
            return target;
        }, {});

    const jsonParse = json => JSON.parse(JSON.stringify(json));

    const jsonStr = json => JSON.stringify(json)

    const base_url = '{{ url(AFFILIATES_COUPONS_BASE_URL) }}';

    createApp({
        item: {},
        $item: {},
        value_rule: 'settings',
        saving: false,
        $el: null,

        modal: null,

        setItem(item) {
            item = item || {
                document: null,
                token: null,
                type: 'ALL',
                discount_rule: 'settings',
                discount_value: null,
                affiliate_rule: 'settings',
                affiliate_value: null,
                start_at_date: null,
                start_at_time: null,
                expires_at_date: null,
                expires_at_time: null,
                max_uses: null,
                max_uses_user: null,
                uses: 0
            };

            this.item = item;

            this.value_rule = item.discount_rule === 'settings' || item.affiliate_rule === 'settings' ? 'settings' : 'manual';

            this.$item = jsonParse(item);
        },

        isChanged() {
            let _new = jsonStr(sortByKeys(filterEmpty(this.item)));
            let _ori = jsonStr(sortByKeys(filterEmpty(this.$item)));
            return _new !== _ori
        },

        resetUser() {
            this.item.user_id = null;
            this.item.document = null;
            this.item.author_name = null;
        },

        add() {
            this.setItem();
            this.showModal();
        },

        edit(item) {
            ['start_at', 'expires_at'].forEach(key => {
                let [_date, _time] = (item[key] || '').split(' ');
                item[`${key}_date`] = (_date || '');
                item[`${key}_time`] = (_time || '').substring(0, 5);
            });

            this.setItem(item);
            this.showModal();
        },

        save() {
            this.saving = true;
            let _this = this;

            let data = jsonParse(this.item);

            let url = base_url + (data.id ? `/${data.id}/edit` : '/create')

            submitForm({
                form: $(this.$el),
                method: 'POST',
                url,
                data,

                onSuccess(data) {
                    _this.close(false);
                    $('.datagrid form').submit();
                },

                onFinish() {
                    _this.saving = false
                }
            });
        },

        close(check = true) {
            if (check && this.isChanged()) {
                let _this = this;
                dialogConfirm(
                    'As alterações não foram salvas! <br/>Ao cancelar as alterações serão perdidas! <br/><br/>Confirma o cancelamento?',
                    () => _this.close(false)
                );

                return;
            }
            this.modal && this.modal.hide();
            resetAllFormValidationErrors($(this.$el));
            this.setItem();
        },

        showModal() {
            this.modal = Modal.getOrCreateInstance(this.$el, {
                backdrop: 'static',
                keyboard: false
            });

            this.updatePlugins();

            this.modal.show();
        },

        findAffiliate() {
            let _this = this;
            findAuthorByDocument(_this.item.document)
                .done(author => {
                    _this.item.user_id = author.id;
                    _this.item.author_name = author.name;
                });
        },

        onTypeChange() {
            // ...
        },

        onValueRuleChange() {
            let rule = this.value_rule === 'settings' ? 'settings' : 'percent';
            this.item.discount_rule = rule;
            this.item.discount_value = '0,00';
            this.item.affiliate_rule = rule;
            this.item.affiliate_value = '0,00';

            this.updatePlugins();
        },

        onChangeRule() {
            this.updatePlugins();
        },

        updatePlugins() {
            let _this = this;

            let moneyFormat = {
                thousands: ".",
                decimal: ",",
                precision: 2,
                allowZero: true,
                reverse: true,
                selectAllOnFocus: true,
            };

            let percentFormat = {
                ...moneyFormat,
                thousands: ""
            };

            $('[name~="document"]', $(this.$el))
                .mask(
                    maskDocumentoOptionsBehavior,
                    maskDocumentOptions
                )
                .on('change', e => _this.item.document = $(e.target).val());

            $('[name~="max_uses"], [name="max_uses_user"]', $(this.$el))
                .mask('########9')
                .on('change', e => _this.item[$(e.target).attr('name')] = $(e.target).val());

            $('[name="discount_value"]', $(this.$el))
                .maskMoney(this.item.discount_rule === 'percent' ? percentFormat : moneyFormat)
                .on('change', e => _this.item.discount_value = $(e.target).val());

            $('[name="affiliate_value"]', $(this.$el))
                .maskMoney(this.item.affiliate_rule === 'percent' ? percentFormat : moneyFormat)
                .on('change', e => _this.item.affiliate_value = $(e.target).val());
        },

        onEscKeyCheck({
            keyCode
        }) {
            if (keyCode === 27) return this.close();
        },

        onMounted() {
            window.$appCoupon = this;
            this.$el = document.querySelector('#modal-coupons-form')
            $(document).on('keydown', this.onEscKeyCheck)
        },

        onUnmounted() {
            $(document).off('keydown', this.onEscKeyCheck)
        }
    }).mount('#modal-coupons-form')
</script>