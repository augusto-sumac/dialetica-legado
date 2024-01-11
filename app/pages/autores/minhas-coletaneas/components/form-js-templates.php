<script id="div-author-line" type="text/x-handlebars-template">
    <div class="row align-items-center">
        <div class="col-auto">
            <button type="button" class="btn btn-sm btn-outline-danger remove-author" data-id="${id}}">
                <span class="fas fa-trash"></span>
            </button>
            <input type="hidden" name="author_ids[]" value="${id}}">
        </div>
        <div class="col">
            <span>${ name }}</span>
        </div>
    </div>
</script>