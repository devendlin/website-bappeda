<!-- app/Views/admin/berita.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Berita</li>
            
                <a href="<?=base_url()?>roomloki/berita/tambah" class="btn btn-primary btn-tambah">
                    Add Post
                </a>    
            
        </ol>
    </nav>
    <div id="notif-berita"></div>
    <div class="table-responsive">
        <table class="table table-striped" id="tabelBerita" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Judul</th>
                    <th>Tanggal</th> <!-- walaupun disembunyikan -->
                    <th></th>
                </tr>
            </thead>
            
        </table>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-labelledby="modalHapusLabel" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-hapus">Hapus</button>
            </div>
            </div>
        </div>
    </div>
    
<?= $this->endSection() ?>
<?= $this->section('content_js') ?>
    <script>
    $(document).ready(function() {
        $('#tabelBerita').DataTable({
            order: [[1, 'desc']],
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= base_url('roomloki/berita/ajaxList') ?>",
                type: "POST",
                data: function(d) {
                    Object.assign(d, getCSRFData());
                }
            },
            columns: [
                { data: 'judul' },
                { data: 'tanggal', visible: false },
                { data: 'aksi', orderable: false, searchable: false }
            ]
        });

    });
    </script>
    <script>
        $(document).on('click', '.btn-hapus', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Data tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url() ?>roomloki/berita/hapus/' + id,
                        type: 'DELETE',
                        success: function (res) {
                            if (res.status === 'ok') {
                                $('#notif-berita').html(`
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        Berita berhasil dihapus.
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                `);
                            } else {
                                $('#notif-berita').html(`
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        Gagal! Tidak dapat menghapus berita
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                `);
                            }
                        },
                        error: function () {
                            $('#notif-berita').html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    Error! Terjadi kesalahan pada server.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `);
                        }
                    });
                }
            }); 
        });
    </script>
<?= $this->endSection() ?>