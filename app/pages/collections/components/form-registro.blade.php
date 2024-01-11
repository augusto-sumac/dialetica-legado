<div id="scope_registro" v-scope v-cloak>
    <div class="form-group row">
        <div class="col-sm-6">
            <h3>REGISTRO</h3>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">ISBN - Livro Físico</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" v-model="store.isbn" name="isbn"
                placeholder="ISBN - Livro Físico" />
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">ISBN - E-Book</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" v-model="store.isbn_e_book" name="isbn_e_book"
                placeholder="ISBN - E-Book" />
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">DOI</label>
        <div class="col-sm-4">
            <input type="text" class="form-control" v-model="store.doi" name="doi"
                placeholder="DOI da Coletânea" />
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-3 col-form-label">Link Livro</label>
        <div class="col-sm-7">
            <input type="text" class="form-control" v-model="store.book_url" name="book_url"
                placeholder="Link dol ivro" />
        </div>
    </div>

</div>

@section('js')
    @parent

    <script type="module">
        import {
            createApp
        } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

        const store = window.{{ $app_store_id }};

        createApp({
            store,
        }).mount('#scope_registro')
    </script>
@endsection
