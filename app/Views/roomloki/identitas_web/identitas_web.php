<!-- app/Views/admin/kategori.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Identitas Web</li>
              
            
        </ol>
    </nav>
    <div class="row d-flex justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Sunting Identitas Web</h6>
                </div>
                <div class="card-body">
                    <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                    <?php endif; ?>

                    <form class="row" action="<?= base_url('roomloki/identitas_web/update') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                    <div class="col-md-6">
                        <input type="hidden" name="id_identitas" value="<?= esc($identitas['id_identitas']) ?>">

                        <div class="form-group">
                            <label>Nama Website</label>
                            <input type="text" name="nama_website" class="form-control" value="<?= esc($identitas['nama_website']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Keywords</label>
                            <textarea name="keywords" class="form-control"><?= esc($identitas['keywords']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="description" class="form-control"><?= esc($identitas['description']) ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3"><?= esc($identitas['alamat']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="<?= esc($identitas['email']) ?>">
                        </div>

                        <div class="form-group">
                            <label>Favicon (Upload file image)</label>
                            <input type="file" name="favicon" class="form-control">
                            <?php if ($identitas['favicon']): ?>
                                <div class="mt-2">
                                    <small>Current Favicon:</small><br>
                                    <?php if(filter_var($identitas['favicon'], FILTER_VALIDATE_URL)): ?>
                                        <img src="<?= $identitas['favicon'] ?>" alt="Favicon" style="max-height: 32px;">
                                    <?php else: ?>
                                        <img src="<?= base_url('uploads/identitas/' . $identitas['favicon']) ?>" alt="Favicon" style="max-height: 32px;">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label>Logo Website (Upload file image)</label>
                            <input type="file" name="logo" class="form-control">
                            <?php if ($identitas['logo']): ?>
                                <div class="mt-2">
                                    <small>Current Logo:</small><br>
                                    <?php if(filter_var($identitas['logo'], FILTER_VALIDATE_URL)): ?>
                                        <img src="<?= $identitas['logo'] ?>" alt="Logo" style="max-height: 80px;">
                                    <?php else: ?>
                                        <img src="<?= base_url('uploads/identitas/' . $identitas['logo']) ?>" alt="Logo" style="max-height: 80px;">
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('content_js') ?>

<?= $this->endSection() ?>


