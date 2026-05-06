<!-- app/Views/admin/halaman.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Halaman</li>
            
                <a href="<?=base_url()?>roomloki/halaman/tambah" class="btn btn-primary btn-tambah">
                    Add Post
                </a>    
            
        </ol>
    </nav>
    <div class="table-responsive">
        <table class="table table-striped" id="tabelHalaman" width="100%" cellspacing="0">
            <thead>
                <tr>
                    <th>Judul</th>
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
        $('#tabelHalaman').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?=base_url()?>roomloki/halaman/ajaxList", // ganti sesuai endpoint kamu
                type: "POST"
            },
            columns: [
                
                { data: 'judul' },
                { data: 'aksi', orderable: false, searchable: false }
            ],
            columnDefs: [
                {
                    targets: 1, // Kolom ke-1 (mulai dari 0)
                    className: 'text-right'
                }
            ]
        });
    });
    </script>
    <script>
        var idHalamanUntukHapus = null;

        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            idHalamanUntukHapus = button.data('id');
        });

        $('#btn-confirm-hapus').on('click', function () {
            if (idHalamanUntukHapus) {
                // Panggil fungsi AJAX atau redirect ke URL hapus
                $.ajax({
                    url: '<?=base_url()?>roomloki/halaman/hapus/' + idHalamanUntukHapus,
                    type: 'delete',
                    dataType: 'json',
                    success: function(response) {
                        $('#modalHapus').modal('hide');
                        // Misalnya reload DataTable atau halaman
                        $('#tabelHalaman').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus data.');
                    }
                });
            }
        });
    </script>
<?= $this->endSection() ?>