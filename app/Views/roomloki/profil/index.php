<!-- app/Views/admin/kategori.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Profil</li>
              
            
        </ol>
    </nav>
    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Sunting Profil</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>

                    <form action="<?= base_url('roomloki/profil/updateProfil') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="<?= esc($user->nama_lengkap) ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="<?= esc($user->email) ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="text" name="no_telp" value="<?= esc($user->no_telp) ?>" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Password Baru (kosongkan jika tidak diganti)</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('content_js') ?>

<?= $this->endSection() ?>


