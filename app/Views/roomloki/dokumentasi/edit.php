<?= $this->extend('roomloki/layout/admin_layout') ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="h4 m-0 text-muted" style="font-weight: 300;">Edit Dokumentasi</div>
</div>

<div class="row">
    <div class="col-md-10">
        <div class="card shadow mb-4 border-0">
            <div class="card-body">
                <form id="formEditDokumentasi" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Judul Kegiatan</label>
                                <input type="text" name="judul" class="form-control" required value="<?= $kegiatan['judul'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Tanggal Kegiatan</label>
                                <input type="date" name="tanggal" class="form-control" required value="<?= $kegiatan['tanggal'] ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Deskripsi Ringkas</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= $kegiatan['deskripsi'] ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Tambahkan Foto Baru</label>
                        <div>
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#thumbnailModal">
                               <i class="fas fa-images"></i> Pilih dari Galeri
                            </button>
                        </div>
                    </div>
                    <div id="foto-galeri-inputs"></div>

                    <label class="font-weight-bold text-dark mt-3">Koleksi Foto Saat Ini</label>
                    <div class="row" id="photo-gallery">
                        <?php foreach ($foto as $f): ?>
                        <div style="position: relative; display:inline-block; margin:5px;" class="photo-item" id="photo-<?= $f['id_foto'] ?>">
                            <img src="<?= base_url('uploads/galeri/' . $f['file_foto']) ?>" style="width:130px;height:130px;object-fit:cover;border:1px solid #ccc;">
                            <button type="button" class="btn-hapus-foto" data-id="<?= $f['id_foto'] ?>" style="position:absolute;top:0;right:0;background:red;color:white;border:none;font-size:12px;padding:0px 5px; z-index:10;">
                                ×
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div id="preview-container" class="row mt-3 border-top pt-3" style="display:none;">
                        <div class="col-12"><label class="text-primary font-weight-bold">Preview Foto Baru:</label></div>
                    </div>

                    <hr>
                    <div class="clearfix">
                        <button type="submit" class="btn btn-sm btn-primary float-right">Simpan Perubahan</button>
                        <a href="<?= base_url('roomloki/dokumentasi') ?>" class="btn btn-sm btn-secondary float-right mr-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="thumbnailModal" tabindex="-1" aria-labelledby="thumbnailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="thumbnailModalLabel">Pilih Foto</h5>
            <button type="button" class="close" aria-label="Close" aria-hidden="true" data-dismiss="modal">×</button>
        </div>
        <div class="modal-body">
            <!-- Form Upload -->
            <form id="upload-thumbnail-form">
            <input type="file" name="file[]" id="upload-thumbnail-input" class="form-control" multiple>
            <small class="text-muted">Max 1MB, JPG/PNG. Bisa pilih lebih dari satu file.</small>
            </form>

            <hr>
            <div id="thumbnail-list" class="gap-2" style="text-align:center; max-height:450px; overflow-y:auto;"></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary" onclick="applySelection()">Terapkan Pilihan (<span id="count-selected">0</span>)</button>
        </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content_js') ?>
<script>
$(document).ready(function() {
    let selectedPhotos = [];

    window.toggleSelection = function(url, el) {
        const idx = selectedPhotos.indexOf(url);
        if (idx > -1) {
            selectedPhotos.splice(idx, 1);
            $(el).css('border', '1px solid #ccc');
            $(el).parent().find('.check-mark').remove();
        } else {
            selectedPhotos.push(url);
            $(el).css('border', '4px solid #007bff');
            $(el).parent().append('<div class="check-mark" style="position:absolute; top:5px; left:5px; background:#007bff; color:white; border-radius:50%; width:24px; height:24px; text-align:center; line-height:24px; font-size:12px; pointer-events: none;"><i class="fas fa-check"></i></div>');
        }
        $('#count-selected').text(selectedPhotos.length);
    };

    window.applySelection = function() {
        renderPreviews();
        $('#thumbnailModal').modal('hide');
    };

    function renderPreviews() {
        const container = $('#preview-container');
        const inputsContainer = $('#foto-galeri-inputs');
        container.empty();
        inputsContainer.empty();
        
        if(selectedPhotos.length > 0) {
            container.show();
            container.append('<div class="col-12"><label class="text-primary font-weight-bold">Preview Foto Baru:</label></div>');
            
            selectedPhotos.forEach(function(url, index) {
                inputsContainer.append(`<input type="hidden" name="foto_galeri[]" value="${url}">`);
                container.append(`
                    <div style="position: relative; display:inline-block; margin:5px;" class="new-preview">
                        <img src="${url}" style="width:130px;height:130px;object-fit:cover;border:2px solid #007bff;">
                        <button type="button" class="btn-remove-preview" data-index="${index}" style="position:absolute;top:0;right:0;background:red;color:white;border:none;font-size:12px;padding:0px 5px; z-index:10;">
                            ×
                        </button>
                    </div>
                `);
            });
        } else {
            container.hide();
        }
    }

    $(document).on('click', '.btn-remove-preview', function() {
        const index = $(this).data('index');
        selectedPhotos.splice(index, 1);
        renderPreviews();
        $('#count-selected').text(selectedPhotos.length);

        // Memaksa thumbnail list me-render ulang untuk sinkron checklist
        resetPagination('#thumbnail-list');
        loadUploadedImages('#thumbnail-list', 'toggleSelection');
    });

    $(document).on('click', '.btn-hapus-foto', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus foto ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?= base_url('roomloki/dokumentasi/hapusFoto') ?>/" + id,
                    type: "DELETE",
                    success: function(res) {
                        $('#photo-' + id).remove();
                        Swal.fire('Terhapus', 'Foto telah dihapus', 'success');
                    }
                });
            }
        });
    });

    $('#upload-thumbnail-input').on('change', function () {
        let files = this.files;
        if(files.length === 0) return;
        
        let uploadsDone = 0;
        let totalFiles = files.length;
        
        // Disable input during upload
        $(this).prop('disabled', true);
        Swal.fire({title: 'Mengunggah...', allowOutsideClick: false, didOpen: () => {Swal.showLoading()}});

        for(let i=0; i<totalFiles; i++){
            let data = new FormData();
            data.append("file", files[i]);

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
                    uploadsDone++;
                    checkUploadDone();
                },
                error: function(err) {
                    uploadsDone++;
                    checkUploadDone();
                }
            });
        }
        
        function checkUploadDone() {
            if(uploadsDone === totalFiles) {
                $('#upload-thumbnail-input').prop('disabled', false).val('');
                Swal.close();
                resetPagination('#thumbnail-list');
                loadUploadedImages('#thumbnail-list', 'toggleSelection');
            }
        }
    });

    $('#thumbnailModal').on('shown.bs.modal', function () {
        $('#count-selected').text(selectedPhotos.length);
        resetPagination('#thumbnail-list');
        loadUploadedImages('#thumbnail-list', 'toggleSelection');
    });
    
    $('#thumbnail-list').on('scroll', function() {
        if ($(this).scrollTop() + $(this).innerHeight() >= this.scrollHeight - 10) {
            loadUploadedImages('#thumbnail-list', 'toggleSelection', currentPage + 1);
        }
    });

    let currentPage = 1;
    let isLoading = false;
    let hasMoreImages = true;

    function resetPagination(containerSelector) {
        currentPage = 1;
        hasMoreImages = true;
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
                    const isSelected = selectedPhotos.includes(url);

                    const deleteBtn = !used ? `
                        <button type="button" onclick="hapusGambarSummernote('${file}', this)" 
                            style="position:absolute;top:0;right:0;background:red;color:white;border:none;font-size:12px;padding:0px 5px; z-index:10;">
                            ×
                        </button>` : '';

                    const borderStyle = isSelected ? 'border: 4px solid #007bff;' : 'border: 1px solid #ccc;';
                    const checkMark = isSelected ? '<div class="check-mark" style="position:absolute; top:5px; left:5px; background:#007bff; color:white; border-radius:50%; width:24px; height:24px; text-align:center; line-height:24px; font-size:12px; pointer-events: none;"><i class="fas fa-check"></i></div>' : '';

                    container.append(`
                        <div style="position: relative; display:inline-block; margin:5px;">
                            <img src="${url}" 
                                style="width:130px;height:130px;object-fit:cover;cursor:pointer;${borderStyle}"
                                onclick="${clickHandler}('${url}', this)">
                            ${deleteBtn}
                            ${checkMark}
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

    window.hapusGambarSummernote = function(filename, btn) {
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
                        Swal.fire('Terhapus!', 'Gambar berhasil dihapus.', 'success');
                    },
                    error: function(err) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus gambar.', 'error');
                    }
                });
            }
        });
    }

    $('#formEditDokumentasi').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        const csrfData = getCSRFData();
        for (let key in csrfData) {
            formData.append(key, csrfData[key]);
        }
        
        $.ajax({
            url: "<?= base_url('roomloki/dokumentasi/update/'.$kegiatan['id_dokumentasi']) ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Sedang menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading(); }
                });
            },
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Dokumentasi diperbarui'
                }).then(() => {
                    window.location.href = res.redirect;
                });
            },
            error: function(xhr) {
                let msg = 'Gagal memperbarui data';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
