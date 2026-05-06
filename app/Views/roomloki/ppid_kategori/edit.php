<?= $this->extend('roomloki/layout/admin_layout') ?>

<?= $this->section('content') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('roomloki/ppid/kategori') ?>">Kategori PPID</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
</nav>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Kategori: <?= $kategori['nama_kategori'] ?></h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url('roomloki/ppid/kategori/update/'.$kategori['id_kategori']) ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label>Nama Kategori</label>
                        <input type="text" name="nama_kategori" class="form-control" value="<?= $kategori['nama_kategori'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3"><?= $kategori['deskripsi'] ?></textarea>
                    </div>
                    <div class="form-group">
                        <label>Material Icon Name</label>
                        <div class="input-group">
                            <input type="text" name="icon" id="icon-input" class="form-control" value="<?= $kategori['icon'] ?>">
                            <div class="input-group-append">
                                <span class="input-group-text"><span class="material-icons" id="icon-preview"><?= $kategori['icon'] ?></span></span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="clearfix">
                        <button type="submit" class="btn btn-sm btn-primary float-right">Simpan Perubahan</button>
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
});
</script>
<?= $this->endSection() ?>
