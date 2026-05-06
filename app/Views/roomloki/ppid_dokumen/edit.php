<?= $this->extend('roomloki/layout/admin_layout') ?>

<?= $this->section('content') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('roomloki/ppid/dokumen') ?>">Dokumen PPID</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-10">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Dokumen: <?= $dokumen['judul_dokumen'] ?></h6>
            </div>
            <div class="card-body">
                <form id="formEditDokumen" action="<?= base_url('roomloki/ppid/dokumen/update/'.$dokumen['id_dokumen']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Judul Dokumen</label>
                                <input type="text" name="judul_dokumen" class="form-control" value="<?= $dokumen['judul_dokumen'] ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kategori Dokumen</label>
                                <select name="id_kategori" class="form-control" required>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id_kategori'] ?>" <?= $cat['id_kategori'] == $dokumen['id_kategori'] ? 'selected' : '' ?>>
                                        <?= $cat['nama_kategori'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Upload</label>
                                <input type="date" name="tgl_upload" class="form-control" value="<?= date('Y-m-d', strtotime($dokumen['tgl_upload'])) ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="d-block">Metode Upload Baru (Jika ingin mengganti file)</label>
                                <?php 
                                    $currentIsDrive = strpos($dokumen['file_pdf'], 'drive:') === 0;
                                ?>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="upload_server" name="upload_method" class="custom-control-input" value="local" <?= !$currentIsDrive ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="upload_server">Upload ke Server (Lokal)</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" id="upload_drive" name="upload_method" class="custom-control-input" value="drive" <?= $currentIsDrive ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="upload_drive">Upload ke Google Drive</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Ganti File PDF (Opsional)</label>
                                <input type="file" name="file_pdf" id="file_pdf" class="form-control-file" accept=".pdf">
                                <small class="text-info d-block mt-1">
                                    <?php 
                                        $isDrive = strpos($dokumen['file_pdf'], 'drive:') === 0;
                                        if ($isDrive) {
                                            $fileId = substr($dokumen['file_pdf'], 6);
                                            $oldUrl = "https://drive.google.com/file/d/" . $fileId . "/view";
                                            $loc = "Google Drive";
                                        } else {
                                            $oldUrl = base_url('uploads/ppid/'.$dokumen['file_pdf']);
                                            $loc = "Server Lokal";
                                        }
                                    ?>
                                    File saat ini (<?= $loc ?>): <a href="<?= $oldUrl ?>" target="_blank" class="font-weight-bold">Lihat PDF</a>
                                </small>
                                <small class="text-muted d-block mt-1">Biarkan kosong jika tidak ingin mengubah file.</small>
                                <small class="text-danger font-weight-bold d-block">Batas: Lokal (Maks 1MB) | Drive (Maks 20MB)</small>
                            </div>
                        </div>
                    </div>

                    <div id="uploadProgress" style="display: none;" class="mb-3">
                        <div class="progress" style="height: 25px; border-radius: 10px; overflow: hidden; background-color: #f0f0f0;">
                            <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%; transition: width 0.3s ease-out;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                        </div>
                        <div class="d-flex align-items-center mt-2">
                            <i id="spinnerStatus" class="fas fa-sync text-primary mr-2"></i>
                            <small class="text-primary font-weight-bold" id="statusUpload">Bersiap...</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Keterangan Tambahan</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= $dokumen['deskripsi'] ?></textarea>
                    </div>

                    <hr>
                    <div class="clearfix">
                        <button type="submit" id="btnSimpan" class="btn btn-sm btn-primary float-right">
                            <i class="fas fa-save mr-1"></i> Simpan Perubahan
                        </button>
                        <a href="<?= base_url('roomloki/ppid/dokumen/' . $dokumen['id_kategori']) ?>" class="btn btn-sm btn-secondary float-right mr-2">Batal</a>
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
    $('#formEditDokumen').on('submit', function(e) {
        e.preventDefault();
        
        const fileInput = document.getElementById('file_pdf');
        const uploadMethod = $('input[name="upload_method"]:checked').val();
        
        if (fileInput.files.length > 0) {
            const fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
            
            if (uploadMethod === 'local' && fileSize > 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'File Terlalu Besar',
                    text: 'Untuk Server Lokal maksimal 1MB. Ukuran file Anda ' + fileSize.toFixed(2) + 'MB. Gunakan opsi "Google Drive".',
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
        
        // Progress UI
        const hasFile = fileInput.files.length > 0;
        if (hasFile) {
            $('#uploadProgress').fadeIn();
            $('#progressBar').css('width', '0%').attr('aria-valuenow', 0).text('0%').removeClass('bg-info').addClass('bg-success');
            $('#spinnerStatus').addClass('fa-spin');
            $('#statusUpload').text('Mengirim data...');
        }
        
        $('#btnSimpan').attr('disabled', true);

        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable && hasFile) {
                        var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
                        
                        let displayPercent = percentComplete;
                        if (uploadMethod === 'drive' && percentComplete >= 99) {
                            displayPercent = 99;
                        }

                        $('#progressBar').css('width', displayPercent + '%').attr('aria-valuenow', displayPercent).text(displayPercent + '%');
                        
                        if (percentComplete < 100) {
                            $('#statusUpload').text('Mengunggah: ' + percentComplete + '%');
                        } else {
                            if (uploadMethod === 'drive') {
                                $('#progressBar').removeClass('bg-success').addClass('bg-info');
                                $('#statusUpload').html('<b>Sedang meneruskan ke Google Drive...</b> (Mohon tunggu)');
                            } else {
                                $('#statusUpload').text('Menyimpan perubahan...');
                            }
                        }
                    }
                }, false);
                return xhr;
            },
            success: function(res) {
                if (hasFile) {
                    $('#progressBar').css('width', '100%').text('100%');
                    $('#statusUpload').text('Pembaruan selesai!');
                }
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Dokumen telah diperbarui.',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = res.redirect || "<?= base_url('roomloki/ppid/dokumen') ?>";
                });
            },
            error: function(xhr) {
                $('#uploadProgress').hide();
                $('#btnSimpan').attr('disabled', false);
                
                let msg = 'Gagal memperbarui dokumen';
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
