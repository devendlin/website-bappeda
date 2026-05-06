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
    <div class="h4 m-0 text-muted" style="font-weight: 300;">Dokumen</div>
    <a href="<?= base_url('roomloki/ppid/dokumen/tambah/' . ($id_kategori ?? '')) ?>" class="btn btn-add-post">
        Add Post
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped" id="tabelPpidDokumen" width="100%" cellspacing="0">
        <thead>
            <tr>
                <th class="pl-0">Judul</th>
                <th class="text-right pr-0"></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Modal Preview PDF -->
<div class="modal fade" id="modalPreviewPdf" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document" style="height: 90vh;">
        <div class="modal-content h-100">
            <div class="modal-header">
                <h5 class="modal-title" id="previewTitle">Pratinjau Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 bg-dark h-100">
                <iframe id="pdfFrame" src="" width="100%" height="100%" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Dokumen?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus dokumen ini secara permanen?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-konfirmasi-hapus">Hapus File</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('content_js') ?>
<script>
$(document).ready(function() {
    const table = $('#tabelPpidDokumen').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('roomloki/ppid/dokumen/ajaxList/' . ($id_kategori ?? '')) ?>",
            type: "POST"
        },
        columns: [
            { data: 'judul' },
            { data: 'aksi', orderable: false, searchable: false }
        ],
        language: {
            search: "Search:",
            searchPlaceholder: "",
            lengthMenu: "Show _MENU_ entries",
            zeroRecords: "No documents found",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "Previous",
                next: "Next"
            }
        }
    });

    $(document).on('click', '.btn-preview', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const title = $(this).data('title');
        $('#previewTitle').text(title);
        $('#pdfFrame').attr('src', url);
        $('#modalPreviewPdf').modal('show');
    });

    $('#modalPreviewPdf').on('hidden.bs.modal', function() {
        $('#pdfFrame').attr('src', '');
    });

    let idHapus = null;
    $(document).on('click', '.btn-hapus', function() {
        idHapus = $(this).data('id');
        $('#modalHapus').modal('show');
    });

    $('#btn-konfirmasi-hapus').on('click', function() {
        if (idHapus) {
            $.ajax({
                url: "<?= base_url('roomloki/ppid/dokumen/hapus') ?>/" + idHapus,
                type: "DELETE",
                success: function(res) {
                    $('#modalHapus').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Berhasil', 'Dokumen telah dihapus', 'success');
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
