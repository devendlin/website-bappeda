<?= $this->extend('roomloki/layout/admin_layout') ?>

<?= $this->section('content') ?>
<style>
    .table td { vertical-align: middle !important; border-top: 1px solid #f4f4f4 !important; }
    .table thead th { border-top: 1px solid #eee !important; border-bottom: 2px solid #eee !important; color: #333; font-weight: 700; text-transform: none; }
    .btn-add-post { border: 1px solid #106a44; color: #106a44; font-weight: 500; border-radius: 6px; padding: 4px 12px; }
    .btn-add-post:hover { background-color: #106a44; color: white; }
    .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { margin-bottom: 1.5rem; }
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div class="h4 m-0 text-muted" style="font-weight: 300;">Agenda Kegiatan</div>
    <a href="<?= base_url('roomloki/agenda/tambah') ?>" class="btn btn-add-post">
        Tambah Agenda
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped" id="tabelAgenda" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th>Judul & Deskripsi</th>
                <th>Tanggal</th>
                <th>Lokasi</th>
                <th>Status</th>
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
                <h5 class="modal-title">Hapus Agenda?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus agenda kegiatan ini?
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
    let table = $('#tabelAgenda').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('roomloki/agenda/ajaxList') ?>",
            type: "POST"
        },
        columns: [
            { data: 'judul' },
            { data: 'tgl_pelaksanaan' },
            { data: 'lokasi' },
            { data: 'is_active' },
            { data: 'aksi', orderable: false, searchable: false }
        ],
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Cari agenda...",
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
                url: "<?= base_url('roomloki/agenda/hapus') ?>/" + idHapus,
                type: "DELETE",
                success: function(res) {
                    $('#modalHapus').modal('hide');
                    // table.ajax.reload(); // Sudah ditangani otomatis oleh layout via reloadTabel
                    Swal.fire('Berhasil', 'Agenda telah dihapus', 'success');
                },
                error: function(xhr) {
                    $('#modalHapus').modal('hide');
                    let msg = 'Gagal menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire('Error', msg, 'error');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
