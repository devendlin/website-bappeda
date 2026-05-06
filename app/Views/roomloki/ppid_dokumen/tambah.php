<?= $this->extend('roomloki/layout/admin_layout') ?>

<?= $this->section('content') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('roomloki/ppid/kategori') ?>">Kategori PPID</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('roomloki/ppid/dokumen/' . ($selected_category ?? '')) ?>">Dokumen</a></li>
        <li class="breadcrumb-item active" aria-current="page">Unggah</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-10">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Unggah Dokumen Baru</h6>
            </div>
            <div class="card-body">
                <form id="formTambahDokumen" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Judul Dokumen</label>
                                <input type="text" name="judul_dokumen" class="form-control" required placeholder="Contoh: Lampiran RKPD 2024">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori Dokumen</label>
                                <select name="id_kategori" class="form-control" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id_kategori'] ?>" <?= $selected_category == $cat['id_kategori'] ? 'selected' : '' ?>>
                                        <?= $cat['nama_kategori'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Upload</label>
                                <input type="date" name="tgl_upload" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="d-block">Metode Upload</label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="upload_server" name="upload_method" class="custom-control-input" value="local" checked>
                                    <label class="custom-control-label" for="upload_server">Upload ke Server (Lokal)</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="upload_drive" name="upload_method" class="custom-control-input" value="drive">
                                    <label class="custom-control-label" for="upload_drive">Upload ke Google Drive</label>
                                </div>
                                <small class="form-text text-muted mt-2">
                                    <i class="fas fa-info-circle"></i> 
                                    <b>Server Lokal:</b> File disimpan di server website (menggunakan storage hosting).<br>
                                    <b>Google Drive:</b> File diupload ke Google Drive Bappeda, database hanya menyimpan link. Tidak membebani server.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>File PDF</label>
                                <input type="file" name="file_pdf" id="file_pdf" class="form-control-file" accept=".pdf" required>
                                <small class="text-info d-block">
                                    <i class="fas fa-info-circle"></i> 
                                    <b>Batas Ukuran:</b> Server Lokal (Maks 1MB) | Google Drive (Maks 20MB)
                                </small>
                            </div>
                        </div>
                    </div>

                    <div id="uploadProgress" style="display: none;" class="mb-3">
                        <div class="progress" style="height: 25px; border-radius: 10px; overflow: hidden; background-color: #f0f0f0;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%; transition: width 0.3s ease-out;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        <div class="d-flex align-items-center mt-2">
                            <i id="spinnerStatus" class="fas fa-cloud-upload-alt text-primary mr-2"></i>
                            <small class="text-primary font-weight-bold" id="statusUpload">Bersiap mengunggah...</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan Tambahan</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>

                    <hr>
                    <div class="clearfix">
                        <button type="submit" id="btnSimpan" class="btn btn-sm btn-primary float-right">
                            <i class="fas fa-save mr-1"></i> Simpan Dokumen
                        </button>
                        <a href="<?= base_url('roomloki/ppid/dokumen/' . ($selected_category ?? '')) ?>" class="btn btn-sm btn-secondary float-right mr-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content_js') ?>
<script>
$(document).ready(function() {
    $('#formTambahDokumen').on('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('file_pdf');
        const uploadMethod = $('input[name="upload_method"]:checked').val();
        
        if (fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
            
            if (uploadMethod === 'local' && fileSize > 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran file Anda (' + fileSize.toFixed(2) + 'MB) melebihi batas 1MB untuk Server Lokal. Silakan gunakan opsi "Google Drive".',
                    confirmButtonText: 'Oke'
                });
                return false;
            }
        }

        const formData = new FormData(this);
        const csrfData = getCSRFData();
        for (let key in csrfData) {
            formData.append(key, csrfData[key]);
        }
        
        // UI Start
        $('#uploadProgress').fadeIn();
        $('#progressBar').css('width', '0%').attr('aria-valuenow', 0).text('0%').removeClass('bg-info').addClass('bg-success');
        $('#btnSimpan').attr('disabled', true);
        $('#spinnerStatus').removeClass('fa-cloud-upload-alt fa-sync fa-spin').addClass('fa-sync fa-spin');
        $('#statusUpload').text('Memulai pengiriman file ke server...');

        $.ajax({
            url: "<?= base_url('roomloki/ppid/dokumen/simpan') ?>",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
                        
                        // Batasi di 99% jika Drive, karena butuh waktu kirim ke Google setelah sampai server kita
                        let displayPercent = percentComplete;
                        if (uploadMethod === 'drive' && percentComplete >= 99) {
                            displayPercent = 99;
                        }

                        $('#progressBar').css('width', displayPercent + '%').attr('aria-valuenow', displayPercent).text(displayPercent + '%');
                        
                        if (percentComplete < 100) {
                            $('#statusUpload').text('Mengunggah ke server: ' + percentComplete + '%');
                        } else {
                            if (uploadMethod === 'drive') {
                                $('#progressBar').removeClass('bg-success').addClass('bg-info');
                                $('#statusUpload').html('File diterima server. <b>Sedang meneruskan ke Google Drive...</b> (Mohon tunggu)');
                            } else {
                                $('#statusUpload').text('Menyimpan dokumen...');
                            }
                        }
                    }
                }, false);
                return xhr;
            },
            success: function(res) {
                $('#progressBar').css('width', '100%').text('100%');
                $('#statusUpload').text('Berhasil disimpan!');
                $('#spinnerStatus').removeClass('fa-spin fa-sync').addClass('fa-check-circle text-success');
                
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Berhasil!',
                        text: 'Dokumen telah tersimpan dengan aman.',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = res.redirect || "<?= base_url('roomloki/ppid/dokumen') ?>";
                    });
                }, 500);
            },
            error: function(xhr) {
                $('#uploadProgress').hide();
                $('#btnSimpan').attr('disabled', false);
                
                let msg = 'Gagal mengunggah file';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors) {
                        msg = Object.values(xhr.responseJSON.errors).join('<br>');
                    } else if (xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                }
                Swal.fire('Gagal', msg, 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
