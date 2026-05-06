<!-- app/Views/admin/tag.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Tag</li>
              
            
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-8">
            <div class="table-responsive">
                <table class="table table-striped" id="tabelTag" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th></th>
                        </tr>
                    </thead>
                    
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-secondary py-2">
                    <h6 class="m-0 font-weight-bold text-white">Tambah Tag</h6>
                </div>
                <div class="card-body">
                    <!-- Notifikasi -->
                    <div id="notif" class="mt-2"></div>
                    <form id="form-tag">
                        <input type="text" name="tag" id="tag" class="form-control" required>
                        <hr>
                        <button type="submit" class="btn btn-sm btn-primary float-right">Tambah</button>
                    </form>

                </div>
            </div>
            
        </div>
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
        $('#tabelTag').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?=base_url()?>roomloki/tags/ajaxList", // ganti sesuai endpoint kamu
                type: "POST"
            },
            columns: [
                
                { data: 'nama_tag' },
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
        $('#form-tag').on('submit', function (e) {
            e.preventDefault();

            let tag = $('#tag').val();

            $.ajax({
                url: '<?= base_url('roomloki/tags/simpan') ?>', // Ganti sesuai route-mu
                type: 'POST',
                data: { nama_tag: tag },
                success: function (res) {
                    $('#notif').html(`  <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            Tag berhasil disimpan!
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>`);
                    $('#form-tag')[0].reset();
                    $('#tabelTag').DataTable().ajax.reload();
                },
                error: function (xhr) {
                    $('#notif').html('<div class="alert alert-danger">Gagal menyimpan tag!</div>');
                }
            });
        });
    </script>

    <script>
        var idTagUntukHapus = null;

        $('#modalHapus').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            idTagUntukHapus = button.data('id');
        });

        $('#btn-confirm-hapus').on('click', function () {
            if (idTagUntukHapus) {
                // Panggil fungsi AJAX atau redirect ke URL hapus
                $.ajax({
                    url: '<?=base_url()?>roomloki/tags/hapus/' + idTagUntukHapus,
                    type: 'delete',
                    dataType: 'json',
                    success: function(response) {
                        $('#modalHapus').modal('hide');
                        // Misalnya reload DataTable atau tag
                        $('#tabelTag').DataTable().ajax.reload();
                    },
                    error: function(xhr) {
                        alert('Gagal menghapus data.');
                    }
                });
            }
        });
    </script>

<?= $this->endSection() ?>