<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('roomloki/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Aplikasi</li>
        </ol>
    </nav>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Aplikasi</h6>
            <button class="btn btn-sm btn-primary" id="btn-tambah" data-toggle="modal" data-target="#modalAplikasi">
                <i class="fas fa-plus"></i> Tambah Aplikasi
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tabelAplikasi" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Aplikasi</th>
                            <th>URL</th>
                            <th>Icon</th>
                            <th width="10%">Urutan</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Aplikasi -->
    <div class="modal fade" id="modalAplikasi" tabindex="-1" role="dialog" aria-labelledby="modalAplikasiLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="formAplikasi">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAplikasiLabel">Tambah Aplikasi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_aplikasi" id="id_aplikasi">
                        <div class="form-group">
                            <label>Nama Aplikasi</label>
                            <input type="text" name="nama_aplikasi" id="nama_aplikasi" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>URL Aplikasi</label>
                            <input type="url" name="url" id="url" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>URL Gambar/Icon</label>
                            <div class="mb-2 img-upload-galery">
                                <input type="hidden" name="gambar" id="gambar-hidden">
                                <div class="gallery" style="display:none; position: relative;">
                                    <img class="img-del" id="gambar-preview" src="" style="width:100px; height: auto; border: 1px solid #ccc; border-radius: 5px;">
                                    <button type="button" class="btn btn-sm btn-danger btn-delete-gambar" 
                                            style="position:absolute; top:5px; left:5px;">
                                        &times;
                                    </button>
                                </div>
                                <button type="button" class="custom-file-upload btn btn-sm btn-outline-info" data-toggle="modal" data-target="#thumbnailModal">
                                    <i class="fa fa-cloud-upload"></i> Choose Icon
                                </button>
                            </div>
                            <small class="text-muted">Gunakan URL icon atau pilih dari galeri.</small>
                        </div>
                        <div class="form-group">
                            <label>Urutan Tampil</label>
                            <input type="number" name="urutan" id="urutan" class="form-control" value="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Galeri -->
    <div class="modal fade" id="thumbnailModal" tabindex="-1" aria-labelledby="thumbnailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="thumbnailModalLabel">Pilih Icon</h5>
                <button type="button" class="close" aria-label="Close" aria-hidden="true" data-dismiss="modal">×</button>
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
<script>
$(document).ready(function() {
    var table = $('#tabelAplikasi').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('roomloki/aplikasi/ajaxList') ?>",
            type: "POST",
            data: function(d) {
                d.<?= csrf_token() ?> = "<?= csrf_hash() ?>";
            }
        },
        columns: [
            { data: 'no', orderable: false },
            { data: 'nama_aplikasi' },
            { data: 'url' },
            { data: 'gambar' },
            { data: 'urutan' },
            { data: 'aksi', orderable: false }
        ]
    });

    $('#btn-tambah').click(function() {
        $('#formAplikasi')[0].reset();
        $('#id_aplikasi').val('');
        $('#gambar-hidden').val('');
        $('#gambar-preview').attr('src', '');
        $('.gallery').hide();
        $('#modalAplikasiLabel').text('Tambah Aplikasi');
    });

    $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var url = $(this).data('url');
        var gambar = $(this).data('gambar');
        var urutan = $(this).data('urutan');

        $('#id_aplikasi').val(id);
        $('#nama_aplikasi').val(nama);
        $('#url').val(url);
        
        if (gambar) {
            $('#gambar-hidden').val(gambar);
            $('#gambar-preview').attr('src', gambar);
            $('.gallery').show();
        } else {
            $('#gambar-hidden').val('');
            $('#gambar-preview').attr('src', '');
            $('.gallery').hide();
        }

        $('#urutan').val(urutan);
        $('#modalAplikasiLabel').text('Edit Aplikasi');
        $('#modalAplikasi').modal('show');
    });

    // Media Picker Logic
    $('#thumbnailModal').on('shown.bs.modal', function () {
        resetPagination('#thumbnail-list');
        loadUploadedImages('#thumbnail-list', 'selectThumbnail');
    });
    
    $('#thumbnail-list').on('scroll', function() {
        if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 10) {
            loadUploadedImages('#thumbnail-list', 'selectThumbnail', currentPage + 1);
        }
    });

    window.selectThumbnail = function(url) {
        $('#gambar-hidden').val(url);
        $('#gambar-preview').attr('src', url);
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
                    const { url } = item;
                    container.append(`
                        <div style="position: relative; display:inline-block; margin:5px;">
                            <img src="${url}" 
                                style="width:130px;height:130px;object-fit:cover;cursor:pointer;border:1px solid #ccc"
                                onclick="${clickHandler}('${url}')">
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

    $('#upload-thumbnail-input').on('change', function () {
        let data = new FormData();
        data.append("file", this.files[0]);

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

    $(document).on('click', '.btn-delete-gambar', function(e) {
        e.preventDefault();
        $('.gallery').hide();
        $('#gambar-hidden').val('');
        $('#gambar-preview').attr('src', '');
    });

    $('#formAplikasi').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= base_url('roomloki/aplikasi/simpan') ?>",
            type: "POST",
            data: $(this).serialize() + "&<?= csrf_token() ?>=<?= csrf_hash() ?>",
            success: function(res) {
                if (res.status == 'ok') {
                    $('#modalAplikasi').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Berhasil', 'Data aplikasi berhasil disimpan', 'success');
                }
            }
        });
    });

    $(document).on('click', '.btn-hapus', function() {
        var id = $(this).data('id');
        Swal.fire({
            title: 'Hapus data?',
            text: "Data aplikasi akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('roomloki/aplikasi/hapus') ?>/" + id,
                    type: "DELETE",
                    data: {
                        "<?= csrf_token() ?>": "<?= csrf_hash() ?>"
                    },
                    success: function(res) {
                        if (res.status == 'ok') {
                            table.ajax.reload();
                            Swal.fire('Terhapus!', 'Data aplikasi telah dihapus.', 'success');
                        }
                    }
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
