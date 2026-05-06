<!-- app/Views/admin/kategori.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Manajemen Users</li>

            <button onclick="openModal()" class="btn btn-primary btn-tambah">Tambah User</button>
              
            
        </ol>
    </nav>
    
    <div id="notif-user"></div>
    <div class="table-responsive">

        <table id="usersTable" class="table table-striped">
        <thead>
            <tr>
            <th>No</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Telepon</th>
            <th>Blokir</th>
            <th>Aksi</th>
            </tr>
        </thead>
        </table>
    </div>
    

    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Form User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="userForm">
                <div class="modal-body">
                <div id="notif-user-modal"></div>
                <div class="mb-3">
                    <input type="hidden" name="mode" id="mode" value="add"> 
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" id="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label for="no_telp" class="form-label">No Telp</label>
                    <input type="text" name="no_telp" id="no_telp" class="form-control" required>
                </div>

                <div class="mb-3 d-none">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" id="role" class="form-select" required>
                    <option value="admin">Admin</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="blokir" class="form-label">Blokir</label>
                    <select name="blokir" id="blokir" class="form-select" required>
                    <option value="N">Tidak</option>
                    <option value="Y">Ya</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password (kosong = tidak diubah)</label>
                    <input type="password" name="password" id="password" class="form-control">
                </div>
                </div>

                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>

            </div>
        </div>
    </div>
<?= $this->endSection() ?>
<?= $this->section('content_js') ?>
<script>
    let table;

    $(document).ready(function() {
        table = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            responsive: true,
            ajax: '<?= site_url('roomloki/users/ajax') ?>',
            order: [[1, 'asc']], // ⏪ urutkan dari kolom username (index ke-1)
            columns: [
                { data: 'no', orderable: true }, // kolom no urut
                { data: 'username' },
                { data: 'nama_lengkap' },
                { data: 'email' },
                { data: 'no_telp' },
                { data: 'blokir' },
                { data: 'aksi', orderable: false, searchable: false }
            ]
        });
    });

    function openModal() {
        $('#userForm')[0].reset();
        $('#mode').val('add'); // ⬅️ tambahkan ini
        $('#userModal').modal('show');
    }

    function editUser(username) {
        $('#mode').val('edit');
        $.ajax({
            url: '<?=base_url()?>roomloki/users/get',
            type: 'POST',
            data: { username: username },
            success: function(data) {
                for (let key in data) {
                    $('#' + key).val(data[key]);
                }
                $('#userModal').modal('show');
                $('#mode').val('edit');
            },
            error: function(xhr) {
                alert('Gagal mengambil data user!');
            }
        });
    }



    $('#userForm').on('submit', function(e){
      e.preventDefault();

      const formData = $(this).serializeArray();
      const csrf = getCSRFData(); // sudah ada di layout utama

      // Tambahkan token CSRF
      for (const key in csrf) {
        formData.push({ name: key, value: csrf[key] });
      }

      $.post('<?= base_url('roomloki/users/save') ?>', $.param(formData), function(res){
        if (res.status === 'success') {
          $('#userModal').modal('hide');
          table.ajax.reload(null, false);

          $('#notif-user').html(`
            <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
              Akun berhasil disimpan.
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
          `);
        } else {
          $('#notif-user-modal').html(`
            <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
              ${res.message || 'Gagal menyimpan akun.'}
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
          `);
        }
      }).fail(function(xhr){
        $('#notif-user-modal').html(`
          <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            Terjadi kesalahan saat mengirim data.
            <button type="button" class="close" data-dismiss="alert">&times;</button>
          </div>
        `);
      });
    });


    function deleteUser(username) {
        Swal.fire({
            title: 'Yakin menghapus?',
            text: 'Data akan dihapus permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?= base_url('roomloki/users/delete/') ?>${encodeURIComponent(username)}`,
                    type: 'DELETE',
                    data: getCSRFData(), // fungsi CSRF dari layout utama
                    success: function(res) {
                        if (res.status === 'success') {
                            showNotif('success', res.message);
                            table.ajax.reload(null, false);
                        } else {
                            showNotif('danger', res.message || 'Gagal menghapus user');
                        }

                        // update CSRF token
                        if (res.csrf_token) {
                            $('meta[name="csrf-token"]').attr('content', res.csrf_token);
                        }
                    },
                    error: function() {
                        showNotif('danger', 'Terjadi kesalahan saat menghapus user');
                    }
                });
            }
        });
    }

    function showNotif(type, msg) {
        $('#notif-user').html(`
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${msg}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);
    }
</script>
<?= $this->endSection() ?>
