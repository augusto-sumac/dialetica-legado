<script>
    (function() {
        var personTypeInputsShow = {
                nome_fantasia: "PJ",
                nome_mae: "PF",
            },

            accountTypeFormLabels = {
                CC: {
                    conta_numero_conta_chave: "Conta",
                },
                PX: {
                    conta_numero_conta_chave: "Chave",
                },
            },

            accountTypeInputsShow = {
                conta_codigo_banco: "CC"
            };

        function adjustClientFormByAccountType() {
            var selected_type = $('form#minha-conta [name="conta_tipo_conta"]:checked').val();

            $(
                '[name="conta_numero_conta_chave"], [name="conta_codigo_banco"], [name="conta_agencia_tipo_chave"], [name*="unknow_"]'
                );

            $.each(
                accountTypeFormLabels[selected_type],
                function(input_name, input_label) {
                    $('form#minha-conta [name="' + input_name + '"]')
                        .closest(".form-group")
                        .find("label.col-form-label")
                        .text(input_label);
                }
            );

            $.each(accountTypeInputsShow, function(input_name, account_type) {
                var show_hide = account_type === selected_type ? "show" : "hide";
                $('form#minha-conta [name="' + input_name + '"]')
                    .closest(".form-group")[show_hide]();
            });

            $('[id^="input_conta_agencia_tipo_chave"]')
                .hide()
                .find('input, select')
                .attr('name', 'unknow_' + (new Date()).getTime());

            $('[id^="input_conta_agencia_tipo_chave_' + selected_type + '"]')
                .show()
                .find('input, select')
                .attr('name', 'conta_agencia_tipo_chave');

            $('select[name="conta_agencia_tipo_chave"]').val('email').trigger('change');
        }

        var maskDocumentoOptionsBehavior = function(v, e, f, o) {
                return "##############################";
            },
            maskDocumentOptions = {
                onKeyPress: function(v, e, f, o) {
                    f.mask(maskDocumentoOptionsBehavior.apply({}, arguments), o);
                },
            };

        $(document).ready(function() {
            $('form#minha-conta [name="conta_tipo_conta"]').on('change', adjustClientFormByAccountType);

            $('[name~="conta_cpf_cnpj"], [name~="conta_cpf_cnpj"]').mask(
                maskDocumentoOptionsBehavior,
                maskDocumentOptions
            );

            /* $(document)
                .on('change', 'select[name="conta_agencia_tipo_chave"]', function() {
                    var isPix = $('form#minha-conta [name="conta_tipo_conta"]:checked').val() === 'PX',
                        isEmail = $(this).val() === 'email';
                    if (!isPix || !isEmail) return;

                    $('[name="conta_numero_conta_chave"]').val('');
                }); */

            adjustClientFormByAccountType();
        });
    })();
</script>
