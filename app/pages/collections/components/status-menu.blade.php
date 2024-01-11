<div id="scope_status_menu" class="card-body border-bottom" v-scope v-cloak v-if="showMenu">
    <div class="arrow-steps">
        <div v-if="isPending || isApproved || isRejected" :class="{ step: true, success: isApproved }">
            <span>Ag. Aprovação</span>
        </div>

        <div v-if="isApproved" class="step success">
            <span>Aprovada</span>
        </div>

        <div v-if="isPending" class="step success available" @click="approve">
            <span>Aprovar</span>
        </div>

        <div v-if="isRejected" class="step danger">
            <span>Rejeitada</span>
        </div>

        <div v-if="isFailed" class="step danger">
            <span>Artigos Insuficientes</span>
        </div>

        <div v-if="isPending" class="step danger available" @click="reject">
            <span>Rejeitar</span>
        </div>

        <div v-if="isPastOfWP" class="step success">
            <span>Ag. Publicação</span>
        </div>

        <div v-if="isActive && store.author_id" :class="{ step: true, 'available primary': isActive }"
            @click="changeStatusTo('WP')">
            <span>Ag. Publicação</span>
        </div>

        <div v-if="isPrevOfIP" class="step">
            <span>Produção</span>
        </div>

        <div v-if="isPastOfIP" class="step success">
            <span>Produção</span>
        </div>

        <div v-if="isPublishable" :class="{ step: true, 'available primary': isPublishable }"
            @click="changeStatusTo('IP')">
            <span>Produção</span>
        </div>

        <div v-if="isPrevOfPU" class="step">
            <span>Publicar</span>
        </div>

        <div v-if="isPastOfPU" class="step success">
            <span>Publicar</span>
        </div>

        <div v-if="isInProduction" :class="{ step: true, 'available primary': isInProduction }"
            @click="changeStatusTo('PU')">
            <span>Publicar</span>
        </div>
    </div>
</div>

@section('css')
    @parent

    <style>
        /* Breadcrups CSS */

        .arrow-steps {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
        }

        .arrow-steps .step {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            flex: 1 0 0%;
            font-size: 12px;
            text-align: center;
            color: #666;
            cursor: default;
            padding: 10px 10px 10px 20px;
            position: relative;
            background-color: #e9ecef;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            transition: background-color 0.2s ease;
            cursor: not-allowed;
            line-height: 18px;
        }

        .arrow-steps .step+.step {
            margin-left: 2px;
        }

        .arrow-steps .step:after,
        .arrow-steps .step:before {
            content: " ";
            position: absolute;
            top: 0;
            right: -17px;
            width: 0;
            height: 0;
            border-top: 20px solid transparent;
            border-bottom: 18px solid transparent;
            border-left: 18px solid #e9ecef;
            z-index: 2;
            transition: border-color 0.2s ease;
        }

        .arrow-steps .step:before {
            right: auto;
            left: 0;
            border-left: 17px solid #fff;
            z-index: 0;
        }

        .arrow-steps .step:first-child:before {
            border: none;
        }

        .arrow-steps .step:last-child:after {
            border: none;
        }

        .arrow-steps .step:first-child {
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .arrow-steps .step:last-child {
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .arrow-steps .step.current {
            color: #fff;
            background-color: #23468c;
        }

        .arrow-steps .step.current:after {
            border-left: 17px solid #23468c;
        }



        .arrow-steps .step.success {
            color: #fff;
            background-color: #00d97e;
        }

        .arrow-steps .step.success:after {
            border-left: 17px solid #00d97e;
        }

        .arrow-steps .step.danger {
            color: #fff;
            background-color: #e63757;
        }

        .arrow-steps .step.danger:after {
            border-left: 17px solid #e63757;
        }

        .arrow-steps .step.primary {
            color: #fff;
            background-color: #2c7be5;
        }

        .arrow-steps .step.primary:after {
            border-left: 17px solid #2c7be5;
        }

        .arrow-steps .step.dark {
            color: #fff;
            background-color: #3b506c;
        }

        .arrow-steps .step.dark:after {
            border-left: 17px solid #3b506c;
        }

        .arrow-steps .step.available:hover {
            cursor: pointer;
            font-weight: bold;
        }
    </style>
@endsection

@section('js')
    @parent

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        const store = window.{{ $app_store_id }};

        createApp({
            store,

            get showMenu() {
                return this.store.id > 1 && this.store.status.length
            },

            get isPending() {
                return this.store.status === 'PE';
            },

            get isApproved() {
                return !['PE', 'RE'].includes(this.store.status);
            },

            get isRejected() {
                return this.store.status === 'RE';
            },

            get isActive() {
                return this.store.status === 'AC';
            },

            get isWaitingProduction() {
                return this.store.status === 'WP';
            },

            get isFailed() {
                return this.store.status === 'FL';
            },

            get isPublishable() {
                if (this.store.author_id) {
                    return !this.isActive && this.isWaitingProduction
                }

                return this.isActive;
            },

            get isInProduction() {
                return this.store.status === 'IP';
            },

            get isPastOfWP() {
                return this.store.author_id && ['WP', 'IP', 'PU'].includes(this.store.status);
            },


            get isPrevOfIP() {
                return this.store.author_id && ['AC'].includes(this.store.status);
            },

            get isPastOfIP() {
                return ['IP', 'PU'].includes(this.store.status);
            },

            get isPrevOfPU() {
                return ['AC', 'WP'].includes(this.store.status);
            },

            get isPastOfPU() {
                return ['PU'].includes(this.store.status);
            },

            approve() {
                dialogConfirm('Confirma a aprovação da coletânea?', () => this.changeStatusTo('AC', true));
            },

            reject() {
                dialogConfirm('Confirma a rejeição da coletânea', () => this.changeStatusTo('RE', true));
            },

            changeStatusTo(status, confirmed = false) {
                const onOk = () => $.put(`/collections/${this.store.id}/change-status`, {
                        status
                    })
                    .done(({
                        status,
                        status_badge
                    }) => {
                        this.store.status = status;
                        this.store.status_badge = status_badge;
                    });

                if (!confirmed) {
                    let msg = 'Confirma a alteração do status da coletânea';

                    if (status === 'WP') {
                        msg = `<div class="text-danger">
                            Esta é uma ação que deve ser executada pelo organizador!
                            <br />
                            <br />
                            Quando é atingido o número de artigos requeridos para publicação, o organizador acionara a função "Solicitar Publicação"!
                            <br /><br />
                            Confirma a <strong>ALTERAÇÃO</strong> do status da coletânea?
                        </div>`
                    }

                    return dialogConfirm(msg, onOk);
                }

                onOk();
            },

            restart() {
                dialogConfirm('Confirma reinicar da coletânea?', () => {
                    $.post(`/sistema/coletaneas/${this.store.id}/reiniciar`)
                        .done(({
                            status,
                            status_badge
                        }) => {
                            this.store.status = status;
                            this.store.status_badge = status_badge;

                            Swal
                                .fire({
                                    icon: 'success',
                                    title: 'Fantástico',
                                    text: 'Coletânea foi reiniciada com sucesso!'
                                });
                        });
                });
            }

        }).mount('#scope_status_menu');
    </script>
@endsection
