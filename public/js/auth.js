$(document).ready(function () {
    $('[name="cpf"]').mask("##############################");
    $('[name="fone"]').mask("####################");
});

function showFormSubmitLoading(form) {
    form.find(":submit, .form-confirm")
        .prop("disabled", true)
        .prepend('<span class="spinner-border spinner-border-sm me-2"></span>');
}

function hideFormSubmitLoading(form) {
    form.find(":submit, .form-confirm")
        .prop("disabled", false)
        .find(".spinner-border")
        .remove();
}
