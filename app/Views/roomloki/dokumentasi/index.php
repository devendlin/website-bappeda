<?= $this->extend('roomloki/layout/admin_layout') ?>

<?= $this->section('content') ?>
<style>
    .table td { vertical-align: middle !important; border-top: 1px solid #f4f4f4 !important; }
    .table thead th { border-top: 1px solid #eee !important; border-bottom: 2px solid #eee !important; color: #333; font-weight: 700; text-transform: none; }
    .btn-add-post { border: 1px solid #8e44ad; color: #8e44ad; font-weight: 500; border-radius: 6px; padding: 4px 12px; }
    .btn-add-post:hover { background-color: #8e44ad; color: white; }
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="h4 m-0 text-muted" style="font-weight: 300;">Dokumentasi Kegiatan</div>
    <a href="<?= base_url('roomloki/dokumentasi/tambah') ?>" class="btn btn-add-post">
        Add Post
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped" id="tabelDokumentasi" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Judul & Dokumentasi</th>
                <th class="text-right"></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Dokumentasi?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus kegiatan ini beserta seluruh fotonya?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-konfirmasi-hapus">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content_js') ?>
<script>
$(document).ready(function() {
    let table = $('#tabelDokumentasi').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('roomloki/dokumentasi/ajaxList') ?>",
            type: "POST"
        },
        columns: [
            { data: 'judul' },
            { data: 'aksi', orderable: false, searchable: false }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari dokumentasi...",
            lengthMenu: "Show _MENU_ entries"
        }
    });

    let idHapus = null;
    $(document).on('click', '.btn-hapus', function() {
        idHapus = $(this).data('id');
        $('#modalHapus').modal('show');
    });

    $('#btn-konfirmasi-hapus').on('click', function() {
        if (idHapus) {
            $.ajax({
                url: "<?= base_url('roomloki/dokumentasi/hapus') ?>/" + idHapus,
                type: "DELETE",
                success: function(res) {
                    $('#modalHapus').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Berhasil', 'Dokumentasi telah dihapus', 'success');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
