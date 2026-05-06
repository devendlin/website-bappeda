<!-- app/Views/admin/berita.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=base_url()?>roomloki/berita">Berita</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit</li>  
            
        </ol>
    </nav>
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif ?>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <form class="row" action="<?= base_url('roomloki/berita/update/' . $berita['id_berita']) ?>" method="post" enctype="multipart/form-data">  
        <?= csrf_field() ?>      
        <div class="col-md-8">
            <input type="text" name="judul" placeholder="Judul" class="form-control mb-2" value="<?= esc($berita['judul']) ?>">
            <textarea class="d-none" id="konten" name="isi_berita"><?= esc($berita['isi_berita']) ?></textarea>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Terbitkan</h6>
                </div>
                <div class="card-body">

                    <select name="kategori" id="kategori" placeholder="Kategori" style="display:none" class="mb-2">
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= esc($k['id_kategori']) ?>" data-seo="<?= esc($k['kategori_seo']) ?>" <?= $k['id_kategori']==$berita['id_kategori'] ? 'selected' : '' ?>>
                                <?= esc($k['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select id="tags" name="tags[]" multiple placeholder="Tag" style="display:none" class="mb-2">
                        <?php $tagTerpilih = explode(',', $berita['tag']); ?>
                        <?php foreach ($tags as $tag): ?>
                            <option value="<?= esc($tag['tag_seo']) ?>" <?= in_array($tag['tag_seo'], $tagTerpilih) ? 'selected' : '' ?>><?= esc($tag['nama_tag']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="mb-2 img-upload-galery">
                        <input type="hidden" name="thumbnail" id="thumbnail-hidden">
                        <div class="gallery">
                                <?php 
                                    if (!empty($berita['gambar'])){ 
                                        $thumnail = esc($berita['gambar']);
                                    }
                                    else{
                                        $thumnail = '';
                                    } 
                                ?>
                            <img class="img-del" id="thumbnail-preview" src="<?=$thumnail?>">
                            <button type="button" class="btn btn-sm btn-danger btn-delete-thumbnail" 
                                    style="position:absolute; top:5px; right:5px;">
                                &times;
                            </button>
                        </div>
                        <button type="button" class="custom-file-upload" data-bs-toggle="modal" data-bs-target="#thumbnailModal">
                           <i class="fa fa-cloud-upload"></i> Choose File
                        </button>
                    </div>
                    <input type="datetime-local" class="form-control" id="tanggal" name="tanggal"  value="<?= esc($berita['tanggal']) ?>" placeholder="Tanggal">
                    <hr>
                    <button type="submit" class="btn btn-sm btn-primary float-right">Terbitkan</button>
                </div>
            </div>
            
        </div>
    </form> 
    <div class="modal fade" id="thumbnailModal" tabindex="-1" aria-labelledby="thumbnailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="thumbnailModalLabel">Pilih Thumbnail</h5>
                <button type="button" class="close" aria-label="Close" aria-hidden="true" data-bs-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <!-- Form Upload -->
                <form id="upload-thumbnail-form">
                <input type="file" name="file" id="upload-thumbnail-input" class="form-control">
                <small class="text-muted">Max 1MB, JPG/PNG</small>
                </form>

                <hr>
                <div id="thumbnail-list" class="gap-2" style="text-align:center; max-height:450px; overflow-y:auto;"></div>
            </div>
            </div>
        </div>
    </div>   
<?= $this->endSection() ?>

<?= $this->section('content_js') ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    
    
    $(document).ready(function() {
        $('#konten').summernote({
            dialogsInBody: true,
            height: null, 
            minHeight: 400, 
            maxHeight: 700,
            codemirror: {
                theme: 'monokai'
            },
            toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear', 'strikethrough', 'superscript', 'subscript']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['link', 'picture', 'video', 'table', 'hr']],
            ['misc', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onImageUpload: function(files) {
                    let data = new FormData();
                    data.append("file", files[0]);
                    const csrf = getCSRFData();
                    for (const [key, value] of Object.entries(csrf)) {
                        data.append(key, value);
                    }
                    $.ajax({
                        url: "<?= base_url('roomloki/berita/upload_image') ?>",
                        method: "POST",
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#konten').summernote('insertImage', response.url);
                            resetPagination('#uploaded-image-container');
                            loadUploadedImages('#uploaded-image-container', 'insertImageAndCloseModal');
                        }
                    });
                }
            }
        });
        
    });

    $('#upload-thumbnail-input').on('change', function () {
        let data = new FormData();
        data.append("file", this.files[0]);

        // Tambahkan CSRF kalau perlu
        const csrf = getCSRFData();
        for (const [key, val] of Object.entries(csrf)) {
            data.append(key, val);
        }

        $.ajax({
            url: "<?= base_url('roomloki/berita/upload_image') ?>",
            method: "POST",
            data: data,
            contentType: false,
            processData: false,
            success: function(res) {
                resetPagination('#thumbnail-list');
                loadUploadedImages('#thumbnail-list', 'selectThumbnail');
            },
            error: function(err) {
                alert("Upload gagal.");
            }
        });
    });

    $(document).on('click', '.btn-delete-thumbnail', function(e) {
        e.preventDefault();
        
        // Kosongkan gambar preview dan input hidden
        $('.gallery').hide();
        $('#thumbnail-hidden').val('');

        // Optional: bisa hapus elemen thumbnail-item kalau mau sembunyikan total
        // $(this).closest('.thumbnail-item').remove();
    });

    $(document).on('shown.bs.modal', '.note-modal', function () {

        let modal = $(this);
            $(this).find('[data-dismiss="modal"]').each(function() {
            $(this)
                .attr('data-bs-dismiss', 'modal') // ganti jadi atribut BS5
                .removeAttr('data-dismiss');      // hapus atribut lama
        });
        // Hanya lanjut jika ini modal insert gambar
        if (!modal.find('.note-image-input').length) return;

        // Tambahkan div jika belum ada
        if (!modal.find('#uploaded-image-list').length) {
            modal.find('.modal-body').append(`
                <div id="uploaded-image-list" style="margin-top:10px; border-top:1px solid #ccc; padding-top:10px;">
                    <strong>Gambar yang sudah diunggah:</strong><br>
                    <div id="uploaded-image-container" style="text-align: center; margin-top:5px; max-height:450px; overflow-y:auto;"></div>
                </div>
            `);
            
            modal.find('#uploaded-image-container').on('scroll', function() {
                if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 10) {
                    loadUploadedImages('#uploaded-image-container', 'insertImageAndCloseModal', currentPage + 1);
                }
            });
        }
        
        resetPagination('#uploaded-image-container');
        loadUploadedImages('#uploaded-image-container', 'insertImageAndCloseModal');
    });

    $('#thumbnailModal').on('shown.bs.modal', function () {
        resetPagination('#thumbnail-list');
        loadUploadedImages('#thumbnail-list', 'selectThumbnail');
    });
    
    $('#thumbnail-list').on('scroll', function() {
        if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 10) {
            loadUploadedImages('#thumbnail-list', 'selectThumbnail', currentPage + 1);
        }
    });

    function selectThumbnail(url) {
        $('#thumbnail-hidden').val(url);
        $('#thumbnail-preview').attr('src', url);
        $('.gallery').show();
        $('#thumbnailModal').modal('hide');
    }

    let currentPage = 1;
    let isLoading = false;
    let hasMoreImages = true;

    function resetPagination(containerSelector) {
        currentPage = 1;
        hasMoreImages = true;
        // $(containerSelector).empty();
    }

    function loadUploadedImages(containerSelector, clickHandler, page = 1) {
        if (isLoading || !hasMoreImages) return;
        isLoading = true;

        $.ajax({
            url: "<?= base_url('roomloki/berita/image_list') ?>?page=" + page,
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                const container = $(containerSelector);
                if (!container.length) return;

                if (page === 1) container.empty();

                res.data.forEach(function(item) {
                    const { url, file, used } = item;

                    const deleteBtn = !used ? `
                        <button onclick="hapusGambarSummernote('${file}', this)" 
                            style="position:absolute;top:0;right:0;background:red;color:white;border:none;font-size:12px;padding:0px 5px;">
                            ×
                        </button>` : '';

                    container.append(`
                        <div style="position: relative; display:inline-block; margin:5px;">
                            <img src="${url}" 
                                style="width:130px;height:130px;object-fit:cover;cursor:pointer;border:1px solid #ccc"
                                onclick="${clickHandler}('${url}')">
                            ${deleteBtn}
                        </div>
                    `);
                });

                hasMoreImages = res.has_more;
                currentPage = res.page;
                isLoading = false;
            },
            error: function(err) {
                console.error("Gagal load daftar gambar:", err);
                isLoading = false;
            }
        });
    }


    function hapusGambarSummernote(filename, btn) {
        Swal.fire({
            title: 'Hapus gambar?',
            text: "Gambar ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('roomloki/berita/hapus_gambar') ?>",
                    method: "DELETE",
                    data: {
                        nama_file: filename,
                        <?= csrf_token() ?>: "<?= csrf_hash() ?>"
                    },
                    success: function(res) {
                        $(btn).parent().remove();
                        Swal.fire(
                            'Terhapus!',
                            'Gambar berhasil dihapus.',
                            'success'
                        );
                    },
                    error: function(err) {
                        Swal.fire(
                            'Gagal!',
                            'Terjadi kesalahan saat menghapus gambar.',
                            'error'
                        );
                    }
                });
            }
        });
    }



    // Fungsi global yang dipanggil saat klik thumbnail
    function insertImageAndCloseModal(url) {
        $('#konten').summernote('insertImage', url);
        // Tutup modal Summernote
        $('.note-modal').modal('hide');
    }
    </script>
    <script>
        $(function () {
            var options = {
                allowClear: true
            };
            $("#kategori").selectize(options);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#tags').selectize({
                plugins: ['remove_button'],
                delimiter: ',',
                persist: false,
                create: true // user bisa menulis tag baru
            });
        });
    </script>
    <script type="text/javascript">
        $(function() {
            // Multiple images preview in browser
            var imagesPreview = function(input, placeToInsertImagePreview) {
                $( ".img-del" ).remove();
                if (input.files) {
                if (input.files[0].name.match(/\.(jpg|jpeg|png|gif|JPG|JPEG|PNG|GIF)$/) ) {
                    if(input.files[0].size>1048576) {
                        $('#note').text('Ukuran file terlalu besar, maksimal 1 MB!');
                        $("#gallery-photo-add").val(null);
                    }
                    else {
                        var filesAmount = input.files.length;
                        if (filesAmount === 0) {
                        $('#note').text('Extension: JPG,JPEG,PNG, Max Ukuran file 1 MB!');
                        $("#gallery-photo-add").val(null);
                        }
                        else if (filesAmount > 4) {
                        $('#note').text('Jumlah file terlalu banyak, maksimal 4 file gambar!');
                        $("#gallery-photo-add").val(null);
                        }else{
                        for (i = 0; i < filesAmount; i++) {
                            var reader = new FileReader()
                            reader.onload = function(event) {
                                $($.parseHTML('<img width="100" class="img-del">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                            }
                            reader.readAsDataURL(input.files[i]);
                            $('#note').text('');
                        }
                        }
                    }
                } else {
                    $('#note').text('Ini bukan file gambar!');
                    $("#gallery-photo-add").val(null);
                }   
                }else $('#note').text('Extension: JPG,JPEG,PNG, Max Ukuran file 1 MB!');
            };

            $('#gallery-photo-add').on('change', function() {
                imagesPreview(this, 'div.gallery');
            });
        });
    </script>
<?= $this->endSection() ?>