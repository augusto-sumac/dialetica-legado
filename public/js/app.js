function findAuthorByDocument(document) {
    document = document.replace(/\D/g, "");

    return $.ajax({
        url: "/collections/author-by-document/" + document,
        method: "GET",
        global: false,
    });
}

function findAuthorByDocumentDialog(onSuccess, store = {}) {
    Swal.fire({
        title: "Buscar pelo CPF/CNPJ/ID do coorganizador",
        input: "text",
        inputAttributes: {
            autocapitalize: "off",
            placeholder: "Digitie o CPF/CNPJ/ID do coorganizador",
        },
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        showConfirmButton: true,
        confirmButtonText: "Buscar coorganizador",
        // showDenyButton: false,
        // denyButtonText: "Cadastrar Novo",
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        preConfirm: (document) => {
            window.onClickNewOrganizer = () =>
                createNewOrganizer(document, onSuccess, store);

            return findAuthorByDocument(document)
                .fail(() => {
                    Swal.showValidationMessage(
                        `<div style="display: block">
                            <div>
                            Nenhum registro encontrado
                            </div>
                            <div>
                                <a href='#' onclick="onClickNewOrganizer()">Cadastrar Novo?</a>
                            </div>
                        </div>`
                    );
                })
                .always(() => {
                    Swal.hideLoading();
                    Swal.enableButtons();
                });
        },
        didOpen: () => {
            $(Swal.getContainer())
                .find("input.swal2-input")
                .mask(maskDocumentoOptionsBehavior, maskDocumentOptions);
        },
    }).then((result) => {
        if (result.isConfirmed) {
            onSuccess(result.value);
        }
    });
}

function createNewOrganizer(document, onSuccess, store) {
    Swal.fire({
        title: "Convidar Organizador",
        html: `<form id="form-pre-organizer">
            <p>
                <span class="fas fa-info-circle"></span>
                Ao concluir o processo será enviado um email ao Organizador para que ele conclua seu cadastro!
            </p>
        
            <div class="form-group text-start">
                <div class="col">
                    <label class="form-label">CPF</label>
                    <input type="text" class="form-control cpf" placeholder="CPF" name="document" readonly value="${document}" />
                </div>
            </div>
        
            <div class="form-group text-start">
                <div class="col">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" placeholder="Nome Completo" name="name" required />
                </div>
            </div>
        
            <div class="form-group text-start">
                <div class="col">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" placeholder="nome@dominio.com" name="email" required />
                </div>
            </div>
        </form>`,
        showCancelButton: true,
        cancelButtonText: "Cancelar",
        showConfirmButton: true,
        confirmButtonText: "Confirmar",
        showLoaderOnConfirm: true,
        allowOutsideClick: false,
        preConfirm: () => {
            let form = $("#form-pre-organizer");
            let data = form.serializeObject();

            data.collection_name = store.name;

            return $.ajax({
                url: "/collections/organizer",
                method: "POST",
                data,
                global: false,
            })
                .fail((res) => {
                    submitFormOnFail(
                        form,
                        { skipNotificationError: true },
                        res
                    );
                    Swal.showValidationMessage(
                        `<div style="display: block">Existem campos inválidos!</div>`
                    );
                })
                .always(() => {
                    Swal.hideLoading();
                    Swal.enableButtons();
                });
        },
        didOpen: () => {
            $(Swal.getContainer())
                .find("input.swal2-input")
                .mask(maskDocumentoOptionsBehavior, maskDocumentOptions);
        },
    }).then((result) => {
        console.log({ result });
        if (result.isConfirmed) {
            onSuccess(result.value.author);
        }
    });
}

let acceptCollectionsInviteItem;
let acceptCollectionsInviteItems;

function acceptCollectionsInvite() {
    if (
        acceptCollectionsInviteItem.require_curriculum === 0 &&
        acceptCollectionsInviteItem.require_curriculum_url === 0 &&
        acceptCollectionsInviteItem.require_role === 0 &&
        acceptCollectionsInviteItems === 1
    ) {
        Toast.close();
        $.put(
            `${baseUrl}/collections/${acceptCollectionsInviteItem.id}/accept`
        );

        acceptCollectionsInviteItem = {};
        acceptCollectionsInviteItems = 0;

        return;
    }

    window.location.href = `${baseUrl}/collections/accept`;
}

$(document).ready(function () {
    $(document).on("ajaxComplete", function (_, xhr) {
        timeSessionRevalidate();

        if (xhr.status === 401) {
            sessionRevalidate();
            return;
        }

        if (
            xhr &&
            xhr.responseJSON &&
            xhr.responseJSON.redirect &&
            xhr.responseJSON.redirect !== "/"
        ) {
            Swal.fire({
                icon: "info",
                title: "Atenção!",
                text: "O sistema está solicitando um redirecionamento do seu acesso. Isso pode ocorrer por diversos motivos. Fique tranquilo(a)!",
            }).then(function () {
                window.location.href = xhr.responseJSON.redirect;
            });
        }
    });

    /**
     * Mantém sessão ativa
     */
    setInterval(function () {
        $.get({
            url: baseUrl + "/ping",
            global: false,
        });
    }, 1 * 60 * 1000);

    timeSessionRevalidate();

    function checkCollectionsPendingInvites() {
        let url = window.location.href;
        if (url.match(/collections\/accept|sistema|auth/gi)) {
            return;
        }

        $.get({
            url: baseUrl + "/collections/check-pending-invites",
            global: false,
        }).done((data) => {
            if (!Array.isArray(data) || data.length === 0) return;

            let [item] = data;
            acceptCollectionsInviteItem = item;
            acceptCollectionsInviteItems = data.length;

            Toast.fire({
                icon: "success",
                title: acceptCollectionsInviteItems > 1 ? null : item.name,
                html: `
                    Você foi convidado para ser organizador ${
                        acceptCollectionsInviteItems > 1
                            ? "em " +
                              acceptCollectionsInviteItems +
                              " coletâneas"
                            : "na coletânea"
                    }.
                    <br />
                    <button class="btn btn-success mt-3" onclick="acceptCollectionsInvite()">
                        <span class="fas fa-check me-1"></span>
                        <strong>${
                            acceptCollectionsInviteItems > 1
                                ? "Gerenciar Convites"
                                : "Aceitar Convite"
                        }</strong>
                    </button>
                `,
                timer: 0,
            });
        });
    }

    checkCollectionsPendingInvites();

    /**
     * Checa coletâneas aguardando aceite
     */
    setInterval(checkCollectionsPendingInvites, 5 * 60 * 1000);

    $(document)
        .on("click", ".btn-cancelar", function (event) {
            event.preventDefault();
            var self = $(this);

            dialogConfirm("Deseja cancelar mesmo?", function () {
                window.location.href = self.attr("href");
            });
            return false;
        })
        .on("click", ".remover-registro", function (event) {
            event.preventDefault();
            var self = $(this);

            dialogConfirm("Confirma a remoção do registro?", function () {
                window.location.href = self.attr("href");
            });
            return false;
        })
        .on("click", ".alert-dimissible .close", function () {
            if ($(this).closest(".alerts-wrapper").size()) {
                $(this).closest(".alerts-wrapper").remove();
                return false;
            }
            $(this).closest(".alert").remove();
            return false;
        })
        .on("click", ".toggle-status", function (e) {
            e.preventDefault();
            e.stopPropagation();

            var el = $(this);

            $.post(
                el.attr("href"),
                {
                    status: el.data("status"),
                },
                function (data) {
                    el.text(data.status === 1 ? "Ativo" : "Inativo");
                    el.data("status", data.status);
                    var cls = data.status === 1 ? "bg-success" : "bg-danger";
                    el.attr("class", "toggle-status badge " + cls);
                }
            );

            return false;
        })
        .on("click", ".modal .form-cancel", function (e) {
            e.preventDefault();
            e.stopPropagation();

            var modal = $(this).closest(".modal");

            if (modal.data("modal")) {
                modal.data("modal").hide();
                modal.find("form")[0].reset();
            }

            return false;
        })
        .on("click", ".logout", function () {
            dialogConfirm("Deseja realmente sair do sistema?", function () {
                window.location.href = baseUrl + "/auth/login";
            });
            return false;
        })
        .on("click", "form .add-new a", function (e) {
            e.preventDefault();
            e.stopPropagation();

            var self = $(this);

            dialogConfirm(
                "Isso irá limpar os dados e reiniciar o cadastro! Está certo disso?",
                function () {
                    var _form = self.closest("form");
                    _form.data("id", null);
                    _form.attr("action", self.attr("href"));
                    _form.find(":input").each(function () {
                        $(this).val("");
                    });

                    $(document).trigger("form:add-new", [_form]);

                    self.closest(".add-new").hide();

                    $(".form-header").text($(".form-header").data("title"));
                }
            );

            return false;
        })
        .on("click", ".sidebar-toggle", function (e) {
            e.preventDefault();
            e.stopPropagation();

            $(".sidebar").toggleClass("open");
            $(this).find(".fas").toggleClass("fa-times");
        })
        .on("click", function (e) {
            if (
                $(".sidebar").hasClass("open") &&
                $(e.target).closest(".sidebar").length === 0
            ) {
                e.preventDefault();
                e.stopPropagation();
                $(".sidebar-toggle").trigger("click");
                return false;
            }
        });
});
