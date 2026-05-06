<!-- app/Views/roomloki/kategori.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Manajemen Iklan</li>
              
            
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Upload</h6>
                </div>
                <div class="card-body">

                    <form id="form-banner" enctype="multipart/form-data">
                      <div class="form-group mb-3">
                          <label class="small font-weight-bold">Judul Banner / Iklan</label>
                          <input type="text" name="judul" class="form-control" placeholder="Contoh: Ucapan Selamat HUT RI" required>
                      </div>
                      <div class="row">
                          <div class="col-md-6 mb-3">
                              <label class="small font-weight-bold">Tipe Tampilan</label>
                              <select name="tipe" class="form-control" required>
                                <option value="panjang">Panjang (Pusat / Slider)</option>
                                <option value="kotak">Kotak (Sidebar / Samping)</option>
                              </select>
                          </div>
                          <div class="col-md-6 mb-3">
                              <label class="small font-weight-bold">Pilih File Gambar</label>
                              <input type="file" name="gambar" class="form-control" required>
                          </div>
                      </div>
                      <div class="form-group mb-3">
                          <label class="small font-weight-bold">Aksi URL (Opsional)</label>
                          <input type="url" name="url" class="form-control" placeholder="https://domain.com/halaman-tujuan">
                          <small class="text-muted">Jika dikosongkan, banner tidak bisa diklik.</small>
                      </div>
                      <button class="btn btn-success px-4" type="submit">
                          <i class="fas fa-upload mr-2"></i> Upload Banner
                      </button>
                    </form>
                    

                    


                </div>
            </div>
        </div>
        <div class="col-md-12 mt-4">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Pratinjau Koleksi Banner</h6>
                </div>
                <div class="card-body">
                    <div id="notif-banner"></div>
                    <div class="row">
                        <!-- Left: Panjang -->
                        <div class="col-md-8">
                            <div class="mb-3 d-flex align-items-center">
                                <span class="badge badge-info p-2 rounded-lg mr-2"><i class="fas fa-arrows-alt-h text-white"></i></span>
                                <h6 class="m-0 font-weight-bold">Banner Panjang (Landscape Slider)</h6>
                            </div>
                            <div id="iklan-panjang" class="row g-3"></div>
                        </div>
                        <!-- Right: Kotak -->
                        <div class="col-md-4 border-left">
                            <div class="mb-3 d-flex align-items-center">
                                <span class="badge badge-warning p-2 rounded-lg mr-2"><i class="fas fa-square text-white"></i></span>
                                <h6 class="m-0 font-weight-bold">Banner Kotak (Sidebar)</h6>
                            </div>
                            <div id="iklan-kotak" class="row g-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('content_js') ?>
<script>
    function fetchBanner() {
      $.get("<?= base_url('roomloki/banner/getAll') ?>", function(res) {
        if (res.status === 'success') {
          renderBanner(res.data.kotak, '#iklan-kotak');
          renderBanner(res.data.panjang, '#iklan-panjang');
        }
      });
    }

    function renderBanner(list, target) {
      const container = $(target);
      container.empty();
      const isLong = target === '#iklan-panjang';
      
      if (list.length === 0) {
          container.append('<div class="col-12 py-4 text-center text-muted italic small border rounded-lg bg-light">Belum ada konten</div>');
          return;
      }

      list.forEach(b => {
        const minHeight = isLong ? '180px' : '300px';
        const item = `<div class="col-md-12 mb-3">
          <div class="rounded-xl border overflow-hidden shadow-sm position-relative group"
            style="
              background: 
                linear-gradient(to bottom, rgba(0,0,0,0) 40%, rgba(0,0,0,0.8) 100%),
                url('<?= base_url('uploads/banner/') ?>${b.gambar}');
              background-size: cover;
              background-position: center;
              min-height: ${minHeight};
              display: flex;
              flex-direction: column;
              justify-content: flex-end;
              padding: 20px;
              color: white;
            ">
            <div class="position-absolute" style="top:10px; right:10px;">
                <button class="btn btn-sm btn-danger shadow" onclick="hapusBanner(${b.id_banner})"><i class="fas fa-trash"></i></button>
            </div>
            <div>
              <div class="font-weight-bold" style="font-size:1rem; line-height:1.2;">${b.judul}</div>
              <div class="small opacity-75 mt-1" style="word-break: break-all;">${b.url ? b.url : '<span class="italic">Tanpa Link</span>'}</div>
            </div>
          </div>
        </div>`;
        container.append(item);
      });
    }

    function hapusBanner(id) {
      Swal.fire({
        title: 'Hapus Banner?',
        text: "Banner akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          const csrfName = $('meta[name="csrf-name"]').attr('content');
          const csrfToken = $('meta[name="csrf-token"]').attr('content');

          $.ajax({
            url: `<?= base_url('roomloki/banner/delete/') ?>${id}`,
            type: 'DELETE',
            data: {
              [csrfName]: csrfToken
            },
            success: function(res) {
              if (res.csrf_token) {
                $('meta[name="csrf-token"]').attr('content', res.csrf_token);
              }

              if (res.status === 'success') {
                $('#notif-banner').html('<div class="alert alert-success">Banner berhasil dihapus</div>');
                fetchBanner();
              } else {
                $('#notif-banner').html('<div class="alert alert-danger">Gagal menghapus banner</div>');
              }
            },
            error: function() {
              $('#notif-banner').html('<div class="alert alert-danger">Terjadi kesalahan saat menghapus banner</div>');
            }
          });
        }
      });
    }

    $('#form-banner').on('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      const csrfData = getCSRFData();
      for (let key in csrfData) {
          formData.append(key, csrfData[key]);
      }
      $.ajax({
        url: "<?= base_url('roomloki/banner/upload') ?>",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
          if (res.status === 'success') {
            $('#notif-banner').html('<div class="alert alert-success">' + res.message + '</div>');
            $('#form-banner')[0].reset();
            fetchBanner();
          } else {
            $('#notif-banner').html('<div class="alert alert-danger">' + res.message + '</div>');
          }
        },
        error: function() {
          $('#notif-banner').html('<div class="alert alert-danger">Terjadi kesalahan saat upload</div>');
        }
      });
    });

    $(document).ready(fetchBanner);
</script>
<?= $this->endSection() ?>


