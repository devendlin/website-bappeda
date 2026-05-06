<!-- app/Views/admin/kategori.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Log Aktivitas</li>

              
            
        </ol>
    </nav>
    
    <div id="notif-user"></div>
    <div class="table-responsive">

        <table id="tabelLog" class="table table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Method</th>
                    <th>Aktivitas</th>
                    <th>URL</th>
                    <th>Data</th>
                    <th>IP Address</th>
                    <th>Waktu</th>
                </tr>
            </thead>
        </table>
    </div>
    

<?= $this->endSection() ?>
<?= $this->section('content_js') ?>
<script>
$(document).ready(function () {
    $('#tabelLog').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= base_url('roomloki/logadmin/ajaxList') ?>",
            type: "POST"
        },
        order: [[7, 'desc']], // kolom waktu
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'username' },
            { 
                data: 'method',
                render: function(data) {
                    let badgeClass = 'badge-secondary';
                    if (data === 'POST') badgeClass = 'badge-primary';
                    if (data === 'DELETE') badgeClass = 'badge-danger';
                    return `<span class="badge ${badgeClass}">${data}</span>`;
                }
            },
            { data: 'aksi' },
            { 
                data: 'url',
                render: function(data) {
                    return `<small class="text-muted">${data}</small>`;
                }
            },
            { 
                data: 'data_payload',
                className: 'text-center',
                orderable: false,
                render: function(data, type, row) {
                    // Cek apakah ada data payload (bukan null, bukan string 'null', bukan string kosong)
                    const hasPayload = data && data !== 'null' && data !== '';
                    const btnClass = hasPayload ? 'btn-info' : 'btn-outline-secondary';
                    const btnText = hasPayload ? 'Detail' : 'Info';
                    
                    // Gunakan base64 untuk passing data agar karakter spesial/kutipan tidak merusak HTML tag
                    const payloadBase64 = hasPayload ? btoa(unescape(encodeURIComponent(data))) : '';
                    const agentBase64 = row.user_agent ? btoa(unescape(encodeURIComponent(row.user_agent))) : '';
                    
                    return `<button class="btn btn-xs ${btnClass} px-3" onclick="viewLogDetail('${payloadBase64}', '${agentBase64}')">${btnText}</button>`;
                }
            },
            { data: 'ip_address' },
            { data: 'waktu' },
        ]
    });
});

function viewLogDetail(payloadB64, agentB64) {
    let payload = '';
    let agent = '';
    
    try {
        if (payloadB64) payload = decodeURIComponent(escape(atob(payloadB64)));
        if (agentB64) agent = decodeURIComponent(escape(atob(agentB64)));
    } catch (e) {
        console.error("Gagal mendecode data log", e);
    }

    let html = '';

    if (payload && payload !== 'null') {
        try {
            // Coba parse jika itu JSON
            let json = JSON.parse(payload);
            html += `<div class="mb-3">
                        <div class="small font-weight-bold text-primary mb-1"><i class="fas fa-database mr-1"></i> Data Kiriman (Payload):</div>
                        <pre style="background:#f8f9fc; padding:12px; border:1px solid #e3e6f0; border-radius:8px; max-height:300px; overflow:auto; font-size:11px; color:#4e73df; line-height:1.5;">${JSON.stringify(json, null, 4)}</pre>
                     </div>`;
        } catch(e) {
            // Jika bukan JSON valid, tampilkan teks mentahnya
            html += `<div class="mb-3">
                        <div class="small font-weight-bold text-primary mb-1"><i class="fas fa-database mr-1"></i> Data Kiriman (Raw):</div>
                        <div style="background:#f8f9fc; padding:12px; border:1px solid #e3e6f0; border-radius:8px; font-size:12px; white-space:pre-wrap;">${payload}</div>
                     </div>`;
        }
    } else {
        html += `<div class="alert alert-light border py-2 mb-3">
                    <small class="text-muted"><i class="fas fa-info-circle mr-1"></i> Tidak ada data kiriman (Akses halaman biasa/GET).</small>
                 </div>`;
    }

    html += `<div class="mt-2">
                <div class="small font-weight-bold text-secondary mb-1"><i class="fas fa-desktop mr-1"></i> Perangkat / Browser:</div>
                <div class="small text-muted p-2 bg-light border rounded" style="word-break: break-all;">${agent || 'Tidak terdeteksi'}</div>
             </div>`;
    
    Swal.fire({
        title: 'Detail Aktivitas Admin',
        html: html,
        width: '650px',
        confirmButtonText: 'Tutup',
        confirmButtonColor: '#4e73df',
        customClass: {
            container: 'my-swal-container'
        }
    });
}

</script>
<?= $this->endSection() ?>
