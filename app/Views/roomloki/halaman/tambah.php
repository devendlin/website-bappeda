<!-- app/Views/admin/halaman.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?=base_url()?>roomloki/halaman">Halaman</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah</li>  
            
        </ol>
    </nav>
    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
    <?php endif ?>
    <form class="row" action="<?= base_url('roomloki/halaman/simpan') ?>" method="post" enctype="multipart/form-data">        
        <div class="col-md-8">
            <input type="text" name="judul" placeholder="Judul" class="form-control mb-2" value="<?= old('judul') ?>">
            <textarea class="d-none" id="konten" name="isi_halaman"><?= old('isi_halaman') ?></textarea>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary py-2">
                    <h6 class="m-0 font-weight-bold text-white">Terbitkan</h6>
                </div>
                <div class="card-body">
                    <div class="img-upload-galery">
                        <label for="gallery-photo-add" class="custom-file-upload">
                            <i class="fa fa-cloud-upload"></i> Choose File
                        </label>
                        <input type="file" name="thumbnail" id="gallery-photo-add">
                        <div class="gallery"></div>
                        <span id="note">Extension: JPG,JPEG,PNG, Max Ukuran file 1 MB!</span>
                    </div>
                    <hr>
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-primary float-right">Terbitkan</button>
                </div>
            </div>
            
        </div>
    </form>    
<?= $this->endSection() ?>

<?= $this->section('content_js') ?>
   <script>
    $(document).ready(function() {
        $('#konten').summernote({
            dialogsInBody: true,
            height: 600,
            codemirror: {
                theme: 'monokai'
            },
            callbacks: {
                onImageUpload: function(files) {
                    let data = new FormData();
                    data.append("file", files[0]);
                    const csrf = getCSRFData();
                    for (const [key, value] of Object.entries(csrf)) {
                        data.append(key, value);
                    }
                    $.ajax({
                        url: "<?= base_url('roomloki/halaman/upload_image') ?>",
                        method: "POST",
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#konten').summernote('insertImage', response.url);
                            loadUploadedImages();
                        }
                    });
                }
            }
        });
        
    });

    $(document).on('shown.bs.modal', '.note-modal', function () {
        let modal = $(this);

        // Hanya lanjut jika ini modal insert gambar
        if (!modal.find('.note-image-input').length) return;

        // Tambahkan div jika belum ada
        if (!modal.find('#uploaded-image-list').length) {
            modal.find('.modal-body').append(`
                <div id="uploaded-image-list" style="margin-top:10px; border-top:1px solid #ccc; padding-top:10px;">
                    <strong>Gambar yang sudah diunggah:</strong><br>
                    <div id="uploaded-image-container" style="text-align: center; margin-top:5px; max-height:325px; overflow-y:auto;"></div>
                </div>
            `);
            loadUploadedImages();
        }
    });

    function loadUploadedImages() {
        $.ajax({
            url: "<?= base_url('roomloki/halaman/image_list') ?>",
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                let container = $('#uploaded-image-container');
                if (!container.length) return;
                container.empty(); // bersihkan sebelum isi ulang

                data.forEach(function(url) {
                    container.append(`
                        <img src="${url}" 
                            style="width:80px; height:80px; object-fit:cover; cursor:pointer; border:1px solid #ccc; padding:2px;position: relative; margin: 5px;"
                            onclick="insertImageAndCloseModal('${url}')"
                            title="Klik untuk insert">
                    `);
                });
            },
            error: function(xhr, status, err) {
                console.error("Gagal load daftar gambar:", err);
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
            $("select").selectize(options);
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