<?php
$is_admin_view = preg_match('/\/sistema/i', urlCurrent());
$logged_author_id = !$is_admin_view ? (int) logged_author()->id : null;
if ((!isset($authors) || empty($authors)) && $logged_author_id) {
    $authors = [['id' => logged_author()->id, 'name' => logged_author()->name]];
}
$collection_url = 'null';
if (isset($token) && $token) {
    $collection_url = "'" . url('/coletanea/' . $token) . "'";
}
?>

<script type="module">
    import {
        reactive
    } from 'https://unpkg.com/petite-vue@0.4.1/dist/petite-vue.es.js?module';

    let id = {{ isset($id) ? (int) $id : 'null' }};
    let status = '{{ isset($status) && $status ? $status : 'NE' }}';

    let name = '{{ isset($name) ? $name : '' }}';
    let description = `{{ isset($description) ? $description : '' }}`;
    let area_id = {{ isset($area_id) ? (int) $area_id : 'null' }};
    let subarea_id = {{ isset($subarea_id) ? (int) $subarea_id : 'null' }};
    let specialty_id = {{ isset($specialty_id) ? (int) $specialty_id : 'null' }};
    let volume = '{{ isset($volume) ? $volume : 1 }}';

    let isbn = '{{ isset($isbn) ? $isbn : '' }}';
    let isbn_e_book = '{{ isset($isbn_e_book) ? $isbn_e_book : '' }}';
    let doi = '{{ isset($doi) ? $doi : '' }}';
    let book_url = '{{ isset($book_url) ? $book_url : '' }}';

    let authors = {{ isset($authors) ? json_encode($authors, true) : '[]' }};
    let author_id = {{ isset($author_id) ? $author_id : 'null' }};
    let is_admin_view = {{ $is_admin_view ? 'true' : 'false' }};
    let logged_author_id = {{ $logged_author_id ?? 'null' }};
    let cover_image = "{{ url($cover_image ?? 'public/img/no-photo-collection.jpeg') }}";
    let logged_author = {{ json_encode(logged_author()) }};
    let expires_at = {{ isset($expires_at) && $expires_at ? "'" . date('d/m/Y', strtotime($expires_at)) . "'" : 'null' }};
    let original_expires_at = {{ isset($expires_at) && $expires_at ? "'" . date('d/m/Y', strtotime($expires_at)) . "'" : 'null' }};
    let created_at = {{ isset($created_at) && $created_at ? "'" . date('Y-m-d', strtotime($created_at)) . "'" : 'null' }};

    if (volume > 1) {
        name = `${name} - VOL ${volume}`;
    }

    let collection_url = {{ $collection_url }};

    const store = reactive({
        id,
        status,

        name,
        description,
        area_id,
        subarea_id,
        specialty_id,

        isbn,
        isbn_e_book,
        doi,
        book_url,

        authors,
        author_id,
        is_admin_view,
        logged_author_id,
        logged_author,

        collection_url,

        created_at,
        expires_at,
        original_expires_at,

        cover_image,

        get isExpired() {
            return this.status === 'AC' && this.original_expires_at && dayjs(this.original_expires_at, 'DD/MM/YYYY').isBefore(dayjs());
        },

        get isAdminView() {
            return this.is_admin_view;
        },

        get loggedAuthorId() {
            return this.logged_author_id;
        },

        get isDefaultCollection() {
            return this.id === 1;
        },

        get readOnly() {
            return !['NE', 'PE', 'AC'].includes(this.status) || this.isDefaultCollection || volume > 1;
        },

        get hasAuthors() {
            return this.authors.length > 0
        },

        get cover_image_backgroud() {
            return { 'background-image': `url(${this.cover_image})` }
        },

        get is_valid_cover_image() {
            return this.cover_image && !this.cover_image.includes('no-photo-collection');
        },

        zoomCoverImage() {
            Swal.fire({
                imageUrl: this.cover_image,
                imageAlt: 'Imagem de capa da coletânea',
                imageWidth: '100%',
                showConfirmButton: false,
                showCloseButton: true,
                customClass: {
                    popup: 'cover-image-zoom'
                }
            });
        },

        downloadCoverImage() {
            if (!this.is_valid_cover_image) return 

            const fileName = this.cover_image.split('/').pop();
            let el = document.createElement("a");
            el.setAttribute("href", this.cover_image);
            el.setAttribute("download", fileName);
            document.body.appendChild(el);
            el.click();
            el.remove();
        }
    });

    window.{{ $app_store_id }} = store;
</script>
