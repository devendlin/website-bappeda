<?= $this->extend('roomloki/layout/admin_layout') ?>

<?= $this->section('content') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('roomloki/ppid/kategori') ?>">Kategori PPID</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Tambah Kategori</h6>
            </div>
            <div class="card-body">
                <form id="formTambahKategori">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" placeholder="Contoh: RKPD" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Material Icon Name</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="icon-input" class="form-control" value="description">
                            <div class="input-group-append">
                                <span class="input-group-text"><span class="fas fa-laugh-wink" id="icon-preview"></span></span>
                            </div>
                        </div>
                        <small class="text-muted">Gunakan nama ikon dari <a href="https://fonts.google.com/icons" target="_blank">Google Material Icons</a></small>
                    </div>
                    <hr>
                    <div class="clearfix">
                        <button type="submit" class="btn btn-sm btn-primary float-right">Simpan</button>
                        <a href="<?= base_url('roomloki/ppid/kategori') ?>" class="btn btn-sm btn-secondary float-right mr-2">Batal</a>
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
    $('#icon-input').on('input', function() {
        $('#icon-preview').text($(this).val() || 'description');
    });

    $('#formTambahKategori').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "<?= base_url('roomloki/ppid/kategori/simpan') ?>",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Kategori berhasil ditambahkan'
                }).then(() => {
                    window.location.href = "<?= base_url('roomloki/ppid/kategori') ?>";
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
