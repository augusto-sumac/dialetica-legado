(function ($) {
    $.fn.selectpicker.Constructor.BootstrapVersion = "5";
    $.fn.dataTable.ext.errMode = "none";

    $.fn.tagName = function () {
        return this.prop("tagName").toLowerCase();
    };

    $.tmpl = function (selector, data) {
        var source = $(selector)
            .html()
            .replace(/\$\$\{/gi, "{{{")
            .replace(/\$\{/gi, "{{");
        var template = Handlebars.compile(source);
        return template(data);
    };
})(jQuery);

function make_select_picker(select) {
    select = $(select);

    var options = select.data() || {};

    if (options.multiple) {
        select.attr("multiple", true);
    }

    select.find('option:contains("elecione")').remove();

    select
        .removeClass("form-select")
        .addClass("form-control btn-light")
        .selectpicker(
            $.extend(
                true,
                {
                    liveSearch: select.find("option").length > 5,
                    selectedTextFormat: "count > 1",
                    virtualScroll: 300,
                    showTick: true,
                    size: "sm",
                },
                options
            )
        );
}

const GlobalLoading = () => {};

GlobalLoading.onKeyPress = (e) => {
    if ($(".global-loading").length === 0) {
        return;
    }

    let { keyCode, metaKey, ctrlKey } = e;

    if ((keyCode === 82 || keyCode === 116) && (metaKey || ctrlKey)) {
        return true;
    }

    if (keyCode === 116) {
        return true;
    }

    e.preventDefault();
    e.stopPropagation();

    return false;
};
GlobalLoading.show = () => {
    let html = `<div class="global-loading d-flex justify-content-center align-items-center w-100 h-100 fixed-top" style="background: rgba(0,0,0,0.15); z-index: 30000">
    <span class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status"></span>
    </div>`;

    $("body").addClass("overflow-hidden");

    if ($(".global-loading").length === 0) {
        $("body").append(html);
    }
};

GlobalLoading.hide = () => {
    $("body").removeClass("overflow-hidden").find(".global-loading").remove();
};

$(document).ready(function () {
    ["keypress", "keyup", "keydown"].map((k) =>
        $(document).on(k, (e) => GlobalLoading.onKeyPress(e))
    );

    var sto_ajaxStart;

    $(document)
        .on("ajaxStart", function () {
            clearTimeout(sto_ajaxStart);
            sto_ajaxStart = setTimeout(function () {
                GlobalLoading.show();
            }, 1);
        })
        .on("ajaxStop", function (...args) {
            clearTimeout(sto_ajaxStart);
            GlobalLoading.hide();
        });

    $(".tabs").each(function (_, tabs) {
        tabs = $(tabs);

        tabs.on("click", ".nav-tabs a", function (e) {
            e.preventDefault();
            e.stopPropagation();

            var self = $(this),
                ref = self.attr("href").replace("#", ""),
                tab = tabs.find("#tab_" + ref);

            tabs.find(".nav-tabs .nav-link").removeClass("active");
            tabs.find('[id^="tab_"]').hide();

            tab.show();
            self.addClass("active");

            $(document).trigger("tab:change", [ref, tab]);

            return false;
        });

        tabs.find(".nav-tabs a.active").first().click();

        setTimeout(function () {
            var hash = window.location.hash;
            if (hash) {
                tabs.find('.nav-tabs a[href="' + hash + '"]')
                    .first()
                    .click();
            }
        }, 250);
    });

    var address_form = $(".address-form");

    address_form.on(
        "change",
        '[name="cep"], [name="zip_code"], :input.cep, :input.zip_code',
        function () {
            var self = $(this),
                loading = self.parents(".form-group").find(".spinner-border"),
                inputs = address_form.find(":input");

            var cep = self.val().replace(/\D/, "") + "";

            if (cep.length !== 8) {
                Swal.fire(
                    "CEP inválido",
                    "O CEP deve ao menos 8 posições!"
                ).then(function () {
                    self.focus();
                    self.select();
                });
                return;
            }

            loading.show();
            inputs.attr("disabled", true).addClass("text-muted");

            $.get({
                url: "https://viacep.com.br/ws/" + cep + "/json",
                global: false,
            })
                .done(function (data) {
                    address_form.find('[name="bairro"]').val(data.bairro);
                    address_form.find('[name="district"]').val(data.bairro);
                    address_form.find('[name="cidade"]').val(data.localidade);
                    address_form.find('[name="city"]').val(data.localidade);
                    address_form.find('[name="city_ibge_id"]').val(data.ibge);
                    address_form.find('[name="endereco"]').val(data.logradouro);
                    address_form.find('[name="street"]').val(data.logradouro);
                    address_form.find('[name="uf"]').val(data.uf);
                    address_form.find('[name="state"]').val(data.uf);

                    address_form.find('[name="numero"]').val("");
                    address_form.find('[name="number"]').val("");
                    address_form.find('[name="complemento"]').val("");
                    address_form.find('[name="complement"]').val("");

                    setTimeout(function () {
                        loading.hide();
                        inputs
                            .attr("disabled", false)
                            .removeClass("text-muted");

                        var fucusIn =
                            data.logradouro === ""
                                ? '[name="endereco"], [name="street"]'
                                : '[name="numero"], [name="number"]';

                        address_form.find(fucusIn).focus();

                        address_form
                            .find(
                                ":input:not([name=cep], [name=zip_code], :input.cep, :input.zip_code)"
                            )
                            .trigger("change");
                    }, 50);
                })
                .fail(function () {
                    Swal.fire(
                        "Serviço Offline",
                        "O serviço de consulta de CEPs está offline! Tente mais tarte ou preencha os dados manualmente"
                    );
                })
                .always(function () {
                    loading.hide();
                    inputs.attr("disabled", false).removeClass("text-muted");
                });
        }
    );

    $(".money-br").maskMoney({
        thousands: ".",
        decimal: ",",
        precision: 2,
        allowZero: true,
        reverse: true,
        selectAllOnFocus: true,
    });

    $('[name*="telefone"], input.telefone').mask(
        maskTelephoneOptionsBehavior,
        maskTelephoneOptions
    );

    // $('[name*="celular"], input.celular').mask("(##) #####-###9");
    $('[name*="celular"], input.celular').mask("####################");

    // $('[name="cpf"], :input.cpf').mask("###.###.###-#9");
    // $('[name="cnpj"], :input.cnpj').mask("##.###.###/####-#9");
    $(
        '[name="cpf"], :input.cpf, [name="cnpj"], :input.cnpj, [name="document"]'
    ).mask("##############################");

    $('[name*="quantidade"], :input.quantidade').mask("000");
    $('[name*="cep"], [name*="zip_code"], :input.cep, :input.zip_code').mask(
        "####################"
        // {
        //     placeholder: "_____-___",
        // }
    );
    $(":input.campo-data").mask("00/00/0000", { placeholder: "__/__/____" });
    $(":input.campo-hora").mask("00:00", { placeholder: "__:__" });

    $("select").map(function () {
        make_select_picker($(this));
    });

    $(document)
        .on("change", "select", function () {
            $(this).data("selectpicker") &&
                $(this).data("selectpicker").refresh();
        })
        .on(
            "focus",
            '[name*="telefone"], [name*="celular"], input.celular, input.phone',
            function () {
                $(this).mask(
                    maskTelephoneOptionsBehavior,
                    maskTelephoneOptions
                );
            }
        )
        .on("focus", '[name*="cep"], [name*="zip_code"]', function () {
            // $(this).mask("00000-000", { placeholder: "_____-___" });
            $(this).mask("####################");
        })
        .on("focus", ":input.campo-data", function () {
            $(this).mask("00/00/0000", { placeholder: "__/__/____" });
        })
        .on("change", ".has-validation-errors :input", function () {
            clearElementValidationError($(this));
        })
        .on("click", ".alert-dimissible .close", function () {
            if ($(this).closest(".alerts-wrapper").size()) {
                $(this).closest(".alerts-wrapper").remove();
                return false;
            }
            $(this).closest(".alert").remove();
            return false;
        })
        .on("click", ".pwd-field .fa-eye", function () {
            $(this).toggleClass("show");
            var next = $(this).next(),
                attr_type = next.attr("type") === "text" ? "password" : "text";
            next.attr("type", attr_type);
        })
        .on("click", ".input-group .fa-eye", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var input = $(this).closest(".input-group").find("input");
            var isText = input.attr("type") === "text";
            $(this)[isText ? "removeClass" : "addClass"]("text-danger");
            input.attr("type", isText ? "password" : "text");
            return false;
        })
        .on("click", "[data-copy-to-clipboard]", function (e) {
            e.preventDefault();
            e.stopPropagation();
            var value = $(this).data("copyToClipboard");
            try {
                navigator.clipboard.writeText(value);
            } catch (e) {}

            if ($(this).data("alert")) {
                alert("Copiado para sua área de transferência");
                return;
            }

            Toast.fire({
                icon: "success",
                title: "Copiado para sua área de transferência",
            });
            return false;
        });

    if (window.innerWidth >= 768) {
        initBsTooltip();
    }
});

function initBsTooltip() {
    [].slice
        .call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        .map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
}

var maskDocumentoOptionsBehavior = function (v, e, f, o) {
        // return v.replace(/\D/g, "").length < 13
        //     ? "000.000.000-09####"
        //     : "00.000.000/0000-00";
        return "##############################";
    },
    maskDocumentOptions = {
        onKeyPress: function (v, e, f, o) {
            f.mask(maskDocumentoOptionsBehavior.apply({}, arguments), o);
        },
    },
    maskTelephoneOptionsBehavior = function (v) {
        // return v.replace(/\D/g, "").length === 11
        //     ? "(00) 00000-0000"
        //     : "(00) 0000-00009";
        return "####################";
    },
    maskTelephoneOptions = {
        onKeyPress: function (v, e, f, o) {
            f.mask(maskTelephoneOptionsBehavior.apply({}, arguments), o);
        },
    };

function dialogConfirm(textOrHtml, onConfirm = function () {}) {
    Swal.fire({
        title: "Atenção",
        html: textOrHtml,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sim",
        cancelButtonText: "Não",
    }).then(function (result) {
        if (result.isConfirmed) {
            onConfirm();
        }
    });
}

const Toast = Swal.mixin({
    customClass: {
        container: "app-toast",
    },
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 5000,
    timerProgressBar: true,
    showCloseButton: true,
    didOpen: (toast) => {
        toast.addEventListener("mouseenter", Swal.stopTimer);
        toast.addEventListener("mouseleave", Swal.resumeTimer);
    },
});

["error", "success", "warning", "info", "question"].forEach((key) => {
    Toast[key] = (title, text = null) => Toast.fire({ text, title, icon: key });
});

function SwalLoading() {
    return Swal.fire({
        title: "Aguarde...",
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        },
    });
}

function getFormTabs(form) {
    if (form.hasClass("tabs")) {
        return form;
    }

    return form.find(".tabs");
}

/**
 * Reset all form validation errors
 *
 * @param jQueryElement form
 */
function resetAllFormValidationErrors(form) {
    getFormTabs(form).find(".nav-tabs li").removeClass("has-validation-errors");

    form.find(":input").each(function (_, element) {
        clearElementValidationError(element);
    });
}

/**
 * Set form input validations errors
 *
 * @param jQueryElement form
 * @param Object errors
 */
function setFormValidationErrors(form, errors) {
    getFormTabs(form).find(".nav-tabs li").removeClass("has-validation-errors");

    let focusElement;

    $.each(errors, function (field, message) {
        if (/\./gi.test(field)) {
            var parts = field.split("."),
                fName = parts.shift();
            field = fName + "[" + parts.join("][") + "]";
        }

        var input = form.find('[name="' + field + '"]');

        message = typeof message === "string" ? message : message[0];

        input
            .addClass("is-invalid")
            .closest('[class^="form-group"]')
            .addClass("has-validation-errors")
            .find(".invalid-feedback")
            .remove();

        var target = input.parent("div");
        if (input.closest(".bootstrap-select").length > 0) {
            target = input.closest(".bootstrap-select").parent("div");

            input.closest(".bootstrap-select").addClass("is-invalid");
        }

        if (input.closest(".form-switch").length > 0) {
            target = input.closest(".form-switch");
        } else if (input.closest(".form-check").length > 0) {
            target = input.closest(".form-check").parent("div");
        }

        target.append('<div class="invalid-feedback">' + message + "</div>");

        focusElement = !focusElement
            ? input
            : input.offset().top > focusElement.offset().top
            ? input
            : focusElement;
    });

    setTimeout(function () {
        getFormTabs(form)
            .find(".nav-tabs li a")
            .each(function ($tab) {
                var ref = $(this).attr("href").replace("#", ""),
                    tab_li = $(this).parent(),
                    tab_div = getFormTabs(form).find('[id="tab_' + ref + '"]'),
                    group_error_class = ".form-group.has-validation-errors";

                if (tab_div.find(group_error_class).length) {
                    tab_li.addClass("has-validation-errors");

                    tab_div
                        .find(group_error_class)
                        .find(":input")
                        .on("change", function () {
                            if (tab_div.find(group_error_class).length <= 1) {
                                tab_li.removeClass("has-validation-errors");
                            }
                        });
                }
            });

        if (focusElement) {
            focusElement.focus();
        }
    }, 100);
}

var sto_session_revalidate = null;
function timeSessionRevalidate() {
    clearTimeout(sto_session_revalidate);
    sto_session_revalidate = setTimeout(function () {
        sessionRevalidate();
    }, 30 * 60 * 1000);
}

function sessionRevalidate(title, text) {
    Swal.fire({
        icon: "warning",
        title: title || "Sua sessão expirou",
        text: text || "Informe sua senha para revalidar",
        allowEscapeKey: false,
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        confirmButtonText: "Enviar",
        input: "password",
        backdrop: "rgba(0,0,0,0.9)",
        inputPlaceholder: "Informe sua senha",
        inputAttributes: {
            autocapitalize: "off",
            autocorrect: "off",
        },
        inputValidator: function (value) {
            return new Promise(function (resolve) {
                if (value.length > 1) {
                    resolve();
                } else {
                    resolve("Informe a sua senha");
                }
            });
        },
        preConfirm: function (senha) {
            return $.post({
                url: baseUrl + "/auth/login",
                global: false,
                data: {
                    email: $('[name="current_user_email"]').val(),
                    senha: senha,
                },
            })
                .done(function () {
                    Swal.fire({
                        icon: "success",
                        title: "Sessão revalidada!",
                        text: "Tenha um ótimo dia",
                    });
                })
                .catch(function (res) {
                    Swal.showValidationMessage("Senha incorreta");
                });
        },
    }).then(timeSessionRevalidate);
}

function clearElementValidationError(element) {
    $(element).removeClass("is-invalid");

    $(element)
        .closest(".form-group")
        .removeClass("has-validation-errors")
        .find(".invalid-feedback")
        .remove();

    $(element)
        .closest(".form-group")
        .find(".is-invalid")
        .removeClass("is-invalid");
}

/**
 * @param object response
 * @returns void
 */
function submitFormOnDone(form, options, response) {
    resetAllFormValidationErrors(form);

    if (response.redirect) {
        window.location.href = response.redirect;
        return;
    }

    if (response.message) {
        Toast.fire({
            icon: "success",
            title: response.message,
        });
    }

    // Se houver necessidade de callback...
    if (options.onSuccess) {
        options.onSuccess(response);
        return false;
    }

    if (form.data("id") > 0 === false) {
        form.trigger("reset");
    }
}

/**
 * @param object response
 * @returns void
 */
function submitFormOnFail(form, options, response) {
    resetAllFormValidationErrors(form);

    response = response.responseJSON || {};

    var errors = response.errors || response.messages || {};

    // Notificando a mensagem
    if (response.message && !options.skipNotificationError) {
        Toast.fire({
            icon: "error",
            title: response.message,
        });
    }

    // Vamos trabalhar então...
    if (errors) {
        // Tem erros de validação para serem exibidos nos campos...
        setFormValidationErrors(form, errors);
    } else {
        // Não deu boa e não tem uma mensagem, então, vamos de msg padrão..
        Toast.fire({
            icon: "error",
            title: "Ocorreu um erro! Tente novamente!",
        });
    }

    if (options.onError) {
        options.onError(response);
    }
}

/**
 * Wrapper para todas as chamadas post com ajax
 *
 * { form, url, data, method, onSuccess, onError }
 */
function submitForm(options) {
    if (!options.form) return;

    var form = options.form;

    var url = options.url || form.attr("action");
    var data = options.data || form.serializeObject();
    var method = options.method || form.attr("method") || "GET";
    var global = options.global || true;

    showButtonLoading(form.find(":submit, .form-confirm"));

    $.ajax({
        url: url,
        data: data,
        type: method,
        global,
    })
        .done(function (response) {
            submitFormOnDone(form, options, response);
        })
        .fail(function (response) {
            submitFormOnFail(form, options, response);
        })
        .always(function () {
            hideButtonLoading(form.find(":submit, .form-confirm"));
            if (options.onFinish) {
                options.onFinish();
            }
        });
}

function showButtonLoading(button) {
    var btn = $(button);
    btn.find(".fas, .far, .fab")
        .filter(":visible")
        .addClass("allowed-to-show")
        .hide();

    var text = btn.text().replace(/\n|\t|\s|\s+/gi, "");

    btn.prop("disabled", true).prepend(
        '<span class="spinner-border spinner-border-sm' +
            (text.length > 1 ? " me-2" : "") +
            '"></span>'
    );
}

function hideButtonLoading(button) {
    $(button).prop("disabled", false).find(".spinner-border").remove();
    $(button).find(".allowed-to-show").show();
}

// Get last element of Array
if (!Array.prototype.last) {
    Array.prototype.last = function () {
        return this[this.length - 1];
    };
}

function mergeMoneyOptions(opts) {
    opts = opts || {};
    opts = {
        lastOutput: opts.lastOutput,
        precision: opts.hasOwnProperty("precision") ? opts.precision : 2,
        showSignal: opts.showSignal,
        suffixUnit:
            (opts.suffixUnit && " " + opts.suffixUnit.replace(/[\s]/g, "")) ||
            "",
        unit: (opts.unit && opts.unit.replace(/[\s]/g, "") + " ") || "",
        zeroCents: opts.zeroCents,
    };
    opts.moneyPrecision = opts.zeroCents ? 0 : opts.precision;
    return opts;
}

function round(number, precision) {
    var factor = Math.pow(10, precision);
    var tempNumber = number * factor;
    var roundedTempNumber = Math.round(tempNumber);
    return roundedTempNumber / factor;
}

/**
 * Convert string money BRL to number
 *
 * @param string value
 * @returns number
 */
function toNumberBr(value, precision) {
    if (!isNaN(value)) value = parseFloat(value).toFixed(2);

    var number = value.toString().replace(/[^0-9,.]/gi, "");

    if (number.match(/\,\d{2}\./gi) || number.match(/\.\d{2}\,/gi)) {
        throw new Error("Invalid Number");
    }

    var has_dot = number.match(/\./gi);
    if (/\..*,/gi.test(number) || (has_dot && has_dot.length > 1)) {
        number = number.replace(/\./gi, "").replace(",", ".");
    }

    var has_comma = number.match(/\,/gi);
    if (/,.*\./gi.test(number) || (has_comma && has_comma.length > 1)) {
        number = number.replace(/,/gi, "");
    }

    if (number.match(/\.+\d{3}/gi) && !number.match(/\d{4}\.+\d{3}/gi)) {
        number = number.replace(/\./gi, "");
    }

    return round(number.replace(/,/gi, "."), precision || 2);
}

/**
 * Convert Number to string BRL formatted
 *
 * @param number value
 * @param object opts
 * @returns string
 */
function toMoneyBr(value, opts) {
    opts = opts || {};

    opts = mergeMoneyOptions(opts);
    opts.delimiter = ".";
    opts.separator = ",";

    if (!isNaN(value)) value = parseFloat(value).toFixed(2);

    var number = value.toString().replace(/[^0-9,.]/gi, "");

    if (number.match(/\,\d{2}\./gi) || number.match(/\.\d{2}\,/gi)) {
        throw new Error("Invalid Number");
    }

    var has_dot = number.match(/\./gi);
    if (/\..*,/gi.test(number) || (has_dot && has_dot.length > 1)) {
        number = number.replace(/\./gi, "").replace(",", ".");
    }

    var has_comma = number.match(/\,/gi);
    if (/,.*\./gi.test(number) || (has_comma && has_comma.length > 1)) {
        number = number.replace(/,/gi, "");
    }

    if (number.match(/\.+\d{3}/gi) && !number.match(/\d{4}\.+\d{3}/gi)) {
        number = number.replace(/\./gi, "");
    }

    number = round(number.replace(/,/gi, "."), opts.precision)
        .toFixed(opts.precision)
        .replace(/\./gi, ",");

    // if separator is in string, make sure we zero-pad to respect it
    var separatorIndex = number.indexOf(opts.separator),
        missingZeros = opts.precision - (number.length - separatorIndex - 1);

    if (separatorIndex !== -1 && missingZeros > 0) {
        number = number + "0" * missingZeros;
    }

    number = number.replace(/[\D]/g, "");

    var clearDelimiter = new RegExp("^(0|\\" + opts.delimiter + ")"),
        clearSeparator = new RegExp("(\\" + opts.separator + ")$"),
        money = number.substr(0, number.length - opts.moneyPrecision),
        masked = money.substr(0, money.length % 3),
        cents = new Array(opts.precision + 1).join("0");

    money = money.substr(money.length % 3, money.length);
    for (var i = 0, len = money.length; i < len; i++) {
        if (i % 3 === 0) {
            masked += opts.delimiter;
        }
        masked += money[i];
    }
    masked = masked.replace(clearDelimiter, "");
    masked = masked.length ? masked : "0";
    var signal = "";
    if (opts.showSignal === true) {
        signal =
            value < 0 || (value.startsWith && value.startsWith("-")) ? "-" : "";
    }

    var beginCents = Math.max(0, number.length - opts.precision),
        centsValue = number.substr(beginCents, opts.precision),
        centsLength = centsValue.length,
        centsSliced =
            opts.precision > centsLength ? opts.precision : centsLength;
    cents = (cents + centsValue).slice(-centsSliced);

    var output = opts.unit + signal + masked + opts.separator + cents;
    return output.replace(clearSeparator, "") + opts.suffixUnit;
}

function forceDownload(data, filename, mime, bom) {
    var blobData = typeof bom !== "undefined" ? [bom, data] : [data];
    var blob = new Blob(blobData, { type: mime || "application/octet-stream" });
    if (typeof window.navigator.msSaveBlob !== "undefined") {
        window.navigator.msSaveBlob(blob, filename);
    } else {
        var blobURL =
            window.URL && window.URL.createObjectURL
                ? window.URL.createObjectURL(blob)
                : window.webkitURL.createObjectURL(blob);
        var tempLink = document.createElement("a");
        tempLink.style.display = "none";
        tempLink.href = blobURL;
        tempLink.setAttribute("download", filename);

        if (typeof tempLink.download === "undefined") {
            tempLink.setAttribute("target", "_blank");
        }

        document.body.appendChild(tempLink);
        tempLink.click();

        setTimeout(function () {
            document.body.removeChild(tempLink);
            window.URL.revokeObjectURL(blobURL);
        }, 0);
    }
}

function generateTableHtml(tableElement, worksheet) {
    let tbody = [];

    ["thead", "tbody", "tfoot"].map(function (elKey) {
        $(tableElement)
            .find(elKey)
            .find("tr")
            .filter(":visible")
            .filter(":not(tr:has(td.filter-line))")
            .each(function (rowIdx, rowEl) {
                let row = $(rowEl),
                    cls = (row.attr("class") + "").split(" ");

                if (
                    !cls.includes("VueTables__filters-row") &&
                    !cls.includes("VueTables__child-row")
                ) {
                    let htmlRow = [];

                    row.find("th, td")
                        .filter(":not(.remove-on-export)")
                        .filter(function () {
                            return (
                                $(this).is(":visible") ||
                                $(this).hasClass("display-only-on-table-export")
                            );
                        })
                        .each(function (colIdx, colEl) {
                            let col = $(colEl).clone(),
                                colspan = col.attr("colspan") || null,
                                text = col
                                    .find(".col_slm")
                                    .remove()
                                    .end()
                                    .find(".sr-only")
                                    .remove()
                                    .end()
                                    .find(".remove-on-export")
                                    .remove()
                                    .end()
                                    .text()
                                    .replace('"', "")
                                    .trim()
                                    .replace(/\s+/gi, " ")
                                    .replace(
                                        /\n|\n+|\r\n|\r\n+|\t\n|\t\n+/gi,
                                        " "
                                    ),
                                tag = col.prop("tagName").toLowerCase();

                            if (text === "") {
                                if (
                                    col.find(".VueTables__child-row-toggler")
                                        .length
                                ) {
                                    text = "+";
                                } else {
                                    text = col.find(".only-on-export").text();
                                }
                            }

                            text = text.replace(/\[BREAKLINE\]/gi, "<br>");

                            colspan = colspan ? ` colspan="${colspan}"` : "";

                            htmlRow.push(`<${tag}${colspan}>${text}</${tag}>`);
                        });

                    tbody.push("<tr>" + htmlRow.join("") + "</tr>");
                }
            });
    });

    let template =
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><meta name=ProgId content=Excel.Sheet> <meta name=Generator content="Microsoft Excel 11"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>${worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--><style>br {mso-data-placement: same-cell;}</style></head><body><table><tbody>${tbody}</tbody></table></body></html>';

    return template
        .replace("${tbody}", tbody.join(""))
        .replace("${worksheet}", worksheet);
}
