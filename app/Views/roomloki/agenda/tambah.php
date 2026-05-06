<?= $this->extend('roomloki/layout/admin_layout') ?>

<?= $this->section('content') ?>
<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="h4 m-0 text-muted" style="font-weight: 300;">Tambah Agenda Kegiatan</div>
</div>

<div class="row">
    <div class="col-md-10">
        <div class="card shadow mb-4 border-0">
            <div class="card-body">
                <form id="formTambahAgenda">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Judul Agenda</label>
                                <input type="text" name="judul" class="form-control" required placeholder="Contoh: Rapat Koordinasi Perencanaan Pembangunan">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Tanggal Pelaksanaan</label>
                                <input type="date" name="tgl_pelaksanaan" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Jam / Waktu</label>
                                <input type="text" name="jam" class="form-control" placeholder="Contoh: 08:30 WIB s/d Selesai">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-dark">Lokasi / Tempat</label>
                                <input type="text" name="lokasi" class="form-control" required placeholder="Contoh: Ruang Rapat Bappeda Lt. 2">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Deskripsi Ringkas</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan detail agenda tersebut secara singkat..."></textarea>
                    </div>

                    <div class="form-group mt-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label font-weight-bold" for="is_active">Aktifkan Agenda (Tampil di Beranda)</label>
                        </div>
                    </div>

                    <hr>
                    <div class="clearfix">
                        <button type="submit" class="btn btn-sm btn-primary float-right">Simpan</button>
                        <a href="<?= base_url('roomloki/agenda') ?>" class="btn btn-sm btn-secondary float-right mr-2">Batal</a>
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
    $('#formTambahAgenda').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        // Sync CSRF
        const csrfData = getCSRFData();
        for (let key in csrfData) {
            formData.append(key, csrfData[key]);
        }
        
        $.ajax({
            url: "<?= base_url('roomloki/agenda/simpan') ?>",
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
                    text: 'Agenda berhasil disimpan'
                }).then(() => {
                    window.location.href = res.redirect;
                });
            },
            error: function(xhr) {
                let msg = 'Gagal menyimpan data';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                if (xhr.responseJSON && xhr.responseJSON.errors) msg = Object.values(xhr.responseJSON.errors).join('<br>');
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
