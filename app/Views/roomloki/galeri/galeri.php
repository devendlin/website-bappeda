<!-- app/Views/admin/kategori.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Menu Utama</li>
              
            
        </ol>
    </nav>
    <div class="row align-items-start">
        <div class="col-md-4 mb-3">
            <div class="card mb-4" >
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Upload</h6>
                    <span class="badge badge-success badge-style-light">Add Foto</span>
                </div>
                <div class="card-body">
                    <!-- Notifikasi -->
                    <div id="notif-kategori" class="mt-2"></div>
                    <form id="form-kategori" enctype="multipart/form-data">
                        <input type="file" name="gambar" class="form-control mb-2" required>
                        <button type="submit" class="btn btn-sm btn-primary float-right">Tambahkan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-3">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Manajemen Gambar</h6>
                    <div>
                        <button onclick="hapusSemuaUnused()" class="btn btn-xs btn-outline-danger mr-2">Hapus Semua Tidak Terpakai</button>
                        <span id="menu-count" class="badge badge-info badge-style-light">foto</span>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div id="thumbnail-list" style="display:inline-block;text-align:center;max-height:none" class="gap-2 "></div>
                    <button id="btn-load-more" class="btn btn-sm btn-secondary mt-3">Load More</button>
                </div>
            </div>
        </div>
    </div>
    
    
    <script>
        function hapusSemuaUnused() {
            Swal.fire({
                title: 'Hapus semua yang tidak terpakai?',
                text: "Tindakan ini akan menghapus permanen seluruh foto yang tidak tertaut ke berita atau dokumentasi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, hapus semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Memproses...',
                        text: 'Sedang menghapus foto-foto tidak terpakai.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "<?= base_url('roomloki/galeri/hapus_semua_unused') ?>",
                        method: "POST",
                        data: {
                            <?= csrf_token() ?>: "<?= csrf_hash() ?>"
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire(
                                    'Berhasil!',
                                    res.message,
                                    'success'
                                ).then(() => {
                                    resetGaleri();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire('Gagal!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        }
    </script>

<?= $this->endSection() ?>
<?= $this->section('content_js') ?>
    <script>
    let offset = 0;
    const limit = 10;

    function resetGaleri() {
        offset = 0;
        $('#thumbnail-list').empty();
        loadGaleri();
    }

    function loadGaleri() {
        $.get("<?= base_url('roomloki/galeri/loadMoreGaleri') ?>", { offset, limit }, function(data) {
            if (data.length === 0) {
                $('#btn-load-more').show();
                return;
            }

            data.forEach(g => {
                const badge = g.dipakai ? '<span class="badge bg-success">Dipakai</span>' : `<button class="badge badge-danger btn-sm mt-1" onclick="hapusGambar('${g.url}', this)">Hapus</button>`;
                
                $('#thumbnail-list').append(`
                    <div class="card text-center" style="position: relative; display:inline-block; margin:5px;padding: 10px;">
                        <img src="${g.url}" style="width:120px;height:100px;object-fit:cover;cursor:pointer;border:1px solid #ccc""><br>
                            <small class="text-muted">${g.waktu}</small><br>
                            ${badge}
                        </div>
                    </div>
                `);
            });

            offset += limit;
        });
    }

    $('#btn-load-more').on('click', loadGaleri);

    // Load pertama
    $(document).ready(function() {
        loadGaleri();
    });
    </script>

    <script>
        function hapusGambar(fileUrl, btn) {
            const filename = decodeURIComponent(fileUrl.split('/').pop());
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
                            Swal.fire(
                                'Terhapus!',
                                'Gambar berhasil dihapus.',
                                'success'
                            );
                        },
                        error: function(err) {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan saat menghapus gambar.',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>

    <script>
        $('#form-kategori').on('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            // Tambahkan CSRF token secara manual ke FormData
            const csrfName = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            formData.append(csrfName, csrfToken);

            $.ajax({
                url: "<?= base_url('roomloki/galeri/upload') ?>",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    if (res.status === 'success') {
                        const g = res.data;

                        const badge = g.dipakai ? 
                            '<span class="badge bg-success">Dipakai</span>' : 
                            `<button class="badge badge-danger btn-sm mt-1" onclick="hapusGambar('${g.url}', this)">Hapus</button>`;

                        $('#thumbnail-list').prepend(`
                            <div class="card text-center" style="position: relative; display:inline-block; margin:5px;padding: 10px;">
                                <img src="${g.url}" style="width:120px;height:100px;object-fit:cover;cursor:pointer;border:1px solid #ccc"><br>
                                <small class="text-muted">${g.waktu}</small><br>
                                ${badge}
                            </div>
                        `);

                        // Reset form
                        form.reset();
                        $('#notif-kategori').html('<div class="alert alert-success">Gambar berhasil ditambahkan!</div>');

                        // Update token jika server mengirim token baru
                        if (res.csrf_token) {
                            document.querySelector('meta[name="csrf-token"]').setAttribute('content', res.csrf_token);
                            localStorage.setItem('csrf_token', res.csrf_token);
                        }

                    } else {
                        $('#notif-kategori').html('<div class="alert alert-danger">' + res.message + '</div>');
                    }
                },
                error: function(err) {
                    $('#notif-kategori').html('<div class="alert alert-danger">Gagal upload gambar</div>');
                }
            });
        });


    </script>

<?= $this->endSection() ?>