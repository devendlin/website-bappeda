

<!-- app/Views/admin/kategori.php -->
<?= $this->extend('roomloki/layout/admin_layout') ?>
<?= $this->section('content') ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page">Menu Utama</li>
              
            
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-4  mb-3">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Kategori</h6>
                    <span class="badge badge-success badge-style-light"><?=count($kategori)?> kategori</span>
                </div>
                <div class="card-body">
                    <!-- Notifikasi -->
                    <div id="notif-kategori" class="mt-2"></div>
                    <form id="form-kategori">
                        <select name="kategori" id="kategori" placeholder="Pilih Kategori" style="display:none" class="mb-2">
                                <option value="" class="d-none"></option>    
                            <?php foreach ($kategori as $k): ?>
                                <option value="<?= esc($k['kategori_seo']) ?>">
                                    <?= esc($k['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <hr>
                        <button type="submit" class="btn btn-sm btn-primary float-right" id="btn-tambah-kategori">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="btn-text">Tambahkan</span>
                        </button>
                    </form>

                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Halaman</h6>
                    <span class="badge badge-success badge-style-light"><?=count($halaman)?> halaman</span>
                </div>
                <div class="card-body">
                    <!-- Notifikasi -->
                    <div id="notif-halaman" class="mt-2"></div>
                    <form id="form-halaman">
                        <select name="halaman" id="halaman" placeholder="Pilih Halaman" style="display:none" class="mb-2">
                                <option value="" class="d-none"></option>
                        <?php foreach ($halaman as $k): ?>
                                <option value="<?= esc($k['judul_seo']) ?>">
                                    <?= esc($k['judul']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <hr>
                        <button type="submit" class="btn btn-sm btn-primary float-right" id="btn-tambah-halaman">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="btn-text">Tambahkan</span>
                        </button>
                    </form>

                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Custom Menu</h6>
                </div>
                <div class="card-body">
                    <!-- Notifikasi -->
                    <div id="notif-custom-menu" class="mt-2"></div>
                    <form id="form-custom-menu">
                        <input type="text" name="nama_menu" class="form-control mb-3" placeholder="Nama Menu" required>
                        <input type="text" name="link" class="form-control" placeholder="Link" required>
                        <hr>
                        <button type="submit" class="btn btn-sm btn-primary float-right" id="btn-tambah-custom-menu">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            <span class="btn-text">Tambahkan</span>
                        </button>
                    </form>

                </div>
            </div>
            
        </div>
        <div class="col-md-8 mb-3">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold">Menu Utama</h6>
                    <?php
                    $totalSubmenu = 0;
                    foreach ($menu as $m) {
                        $totalSubmenu += count($m['submenu']);
                    }
                    ?>
                    <span>
                        <span id="menu-utama" class="badge badge-primary badge-style-light"><?= count($menu) ?> utama</span>
                        <span id="menu-submenu" class="badge badge-info badge-style-light"><?= $totalSubmenu ?> submenu</span>
                    </span>
                </div>
                <div class="card-body">
                    <div id="notif-menu" class="mt-2"></div>
                    <ul id="menu-list" class="list-group">
                        <?php foreach ($menu as $m): ?>
                            <li class="list-group-item" data-id="<?= $m['id_main'] ?>" data-type="main" id="menu-<?= $m['id_main'] ?>">
                                <div class="d-flex justify-content-between align-items-center menu-item">
                                    <span><i class="fas fa-bars mx-3"></i> <?= esc($m['nama_menu']) ?></span>
                                    <button class="badge badge-style-light btn-hapus text-danger" data-id="<?= $m['id_main'] ?>">
                                        <i class="fas fa-trash-alt"></i> hapus
                                    </button>
                                </div>

                                <!-- Submenu -->
                                <ul class="list-group ml-4 sortable">
                                    <?php foreach ($m['submenu'] as $s): ?>
                                        <li class="list-group-item" data-id="<?= $s['id_sub'] ?>" data-type="sub" id="menu-<?= $s['id_sub'] ?>">
                                            <div class="d-flex justify-content-between align-items-center menu-item">
                                                <span><i class="fas fa-bars mx-3"></i> <?= esc($s['nama_sub']) ?></span>
                                                <button class="badge badge-style-light btn-hapus text-danger" data-id="<?= $s['id_sub'] ?>" data-sub="1">
                                                    <i class="fas fa-trash-alt"></i> hapus
                                                </button>
                                            </div>
                                            <ul class="list-group ml-4 sortable"></ul>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <hr>
                    <button id="save-order" class="btn btn-sm btn-primary mt-2 float-right">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Simpan Urutan</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    
<?= $this->endSection() ?>
<?= $this->section('content_js') ?>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        $(document).ready(function () {
            updateMenuCount(); // panggil pertama kali
        });

    </script>
    <script>
        $(function () {
            let options = {
                allowEmptyOption: true,
            };

            // Aktifkan selectize di semua <select>
            $('select').selectize(options);

        });


        function initSortable() {
            document.querySelectorAll('#menu-list, #menu-list ul').forEach(el => {
                if (el._sortable) {
                    el._sortable.destroy(); // destroy previous instance
                }
                el._sortable = Sortable.create(el, {
                    group: {
                        name: 'nested',
                        pull: true,
                        put: true
                    },
                    animation: 150,
                    fallbackOnBody: true,
                    swapThreshold: 0.65,
                    handle: '.menu-item',
                    draggable: 'li',
                    onEnd: function () {
                        updateItemTypes();
                    }
                });
            });
        }

        function updateItemTypes() {
            $('#menu-list > li').each(function () {
                $(this).attr('data-type', 'main');
                $(this).find('ul > li').attr('data-type', 'sub');
            });
        }

        function serializeMenu($ul) {
            const result = [];

            $ul.children('li').each(function (i) {
                const li = $(this);
                const id = parseInt(li.data('id'));
                const type = li.attr('data-type');

                const childUl = li.children('ul');

                const item = {
                    id: id,
                    type: type,
                    urutan: i + 1,
                    children: []
                };

                if (childUl.length) {
                    item.children = serializeMenu(childUl);
                }

                result.push(item);
            });

            return result;
        }

        initSortable();

        $('#save-order').on('click', function () {
            updateItemTypes(); // pastikan data-type sudah benar sebelum simpan

            const data = serializeMenu($('#menu-list'));
            // Disable tombol dan tampilkan spinner
            let $btn = $('#save-order');
            $btn.prop('disabled', true);
            $btn.find('.spinner-border').removeClass('d-none');
            $btn.find('.btn-text').text('Menyimpan...');

            $.ajax({
                url: '<?= base_url('roomloki/menu/simpan_urutan') ?>',
                method: 'POST',
                data: { data: JSON.stringify(data) },
                dataType: 'json',
                success: function (res) {
                    // Kembalikan tombol seperti semula
                    $btn.prop('disabled', false);
                    $btn.find('.spinner-border').addClass('d-none');
                    $btn.find('.btn-text').text('Simpan Urutan');
                    $('#notif-menu').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Urutan menu berhasil diperbarui.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                    updateMenuCount();
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    // Kembalikan tombol seperti semula
                    $btn.prop('disabled', false);
                    $btn.find('.spinner-border').addClass('d-none');
                    $btn.find('.btn-text').text('Simpan Urutan');
                    $('#notif-menu').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Gagal menyimpan urutan menu.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                }
            });
        });




        function updateMenuCount() {
            const mainMenus = $('#menu-list > li').length;
            let subMenus = 0;

            $('#menu-list > li').each(function () {
                subMenus += $(this).find('ul > li').length;
            });

            $('#menu-utama').text(`${mainMenus} utama`);
            $('#menu-submenu').text(`${subMenus} submenu`);
        }

        $(document).on('click', '.btn-hapus', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: "Data ini tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus saja!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('roomloki/menu/hapus') ?>/' + id,
                        type: 'DELETE',
                        success: function (res) {
                            // Tampilkan notifikasi
                            $('#notif-menu').html(`
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    Menu berhasil dihapus.
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            `);
                            // Hapus elemen dari DOM
                            $('#menu-' + id).remove();
                            updateMenuCount();
                        },
                        error: function () {
                            $('#notif-menu').html(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <b>Gagal!</b> Terjadi Kesalahan Hapus.
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
    <script>
    $('#form-kategori').on('submit', function(e) {
        e.preventDefault();
        let selectize = $('#kategori')[0].selectize;
        let kategori_seo = selectize.getValue();
        let nama_kategori = selectize.options[kategori_seo]?.text.trim() || '';
        
        if (!kategori_seo) {
            selectize.focus();
            return;
        }

        // Disable tombol dan tampilkan spinner
        let $btn = $('#btn-tambah-kategori');
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.btn-text').text('Menyimpan...');

        $.ajax({
            url: '<?= base_url('roomloki/menu/tambahkan_kategori') ?>',
            type: 'POST',
            data: {
                kategori_seo: kategori_seo,
                nama_kategori: nama_kategori
            },
            success: function(res) {
                // Kembalikan tombol seperti semula
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
                $btn.find('.btn-text').text('Tambahkan');

                if (res.status === 'ok') {
                    $('#menu-list').prepend(`
                        <li class="list-group-item" data-id="${res.data.id_main}" data-type="main" id="menu-${res.data.id_main}">
                            <div class="d-flex justify-content-between align-items-center menu-item">
                                <span><i class="fas fa-bars mx-3"></i> ${nama_kategori}</span>
                                <button class="badge badge-style-light btn-hapus text-danger" data-id="${res.data.id_main}">
                                    <i class="fas fa-trash-alt"></i> hapus
                                </button>
                            </div>
                            <ul class="list-group ml-4 sortable"></ul>
                        </li>
                    `);
                    $('#notif-kategori').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Menu berhasil ditambahkan.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                    updateMenuCount();
                    initSortable();
                } else {
                    $('#notif-kategori').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${res.error}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                }
            },
            error: function() {
                // Kembalikan tombol seperti semula
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
                $btn.find('.btn-text').text('Tambahkan');

                $('#notif-kategori').html(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal menambahkan menu.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
            }
        });
    });

    
    $('#form-halaman').on('submit', function(e) {
        e.preventDefault();
        let selectize = $('#halaman')[0].selectize;
        let halaman_seo = selectize.getValue();
        let nama_halaman = selectize.options[halaman_seo]?.text.trim() || '';
        
        if (!halaman_seo) {
            selectize.focus();
            return;
        }
        // Disable tombol dan tampilkan spinner
        let $btn = $('#btn-tambah-halaman');
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.btn-text').text('Menyimpan...');
        $.ajax({
            url: '<?= base_url('roomloki/menu/tambahkan_halaman') ?>',
            type: 'POST',
            data: {
                halaman_seo: halaman_seo,
                nama_halaman: nama_halaman
            },
            success: function(res) {
                if (res.status === 'ok') {
                    // Kembalikan tombol seperti semula
                    $btn.prop('disabled', false);
                    $btn.find('.spinner-border').addClass('d-none');
                    $btn.find('.btn-text').text('Tambahkan');
                    // Tambahkan elemen baru ke daftar menu
                    $('#menu-list').prepend(`
                        <li class="list-group-item" data-id="${res.data.id_main}" data-type="main" id="menu-${res.data.id_main}">
                            <div class="d-flex justify-content-between align-items-center menu-item">
                                <span><i class="fas fa-bars mx-3"></i> ${nama_halaman}</span>
                                <button class="badge badge badge-style-light btn-hapus text-danger" data-id="${res.data.id_main}">
                                    <i class="fas fa-trash-alt"></i> hapus
                                </button>
                            </div>
                            <ul class="list-group ml-4 sortable"></ul>
                        </li>
                    `);
                    $('#notif-halaman').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Menu berhasil ditambahkan.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                    updateMenuCount();
                    initSortable();
                } else {
                    $('#notif-halaman').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${res.error}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                }
            },
            error: function() {
                // Kembalikan tombol seperti semula
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
                $btn.find('.btn-text').text('Tambahkan');
                $('#notif-halaman').html(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal menambahkan menu.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
            }
        });
    });

    $('#form-custom-menu').on('submit', function(e) {
        e.preventDefault();

        let nama_menu = $('input[name="nama_menu"]').val().trim();
        let link = $('input[name="link"]').val().trim();

        

        let $btn = $('#btn-tambah-custom-menu');
        $btn.prop('disabled', true);
        $btn.find('.spinner-border').removeClass('d-none');
        $btn.find('.btn-text').text('Menyimpan...');

        $.ajax({
            url: '<?= base_url('roomloki/menu/tambahkan_custom_menu') ?>',
            type: 'POST',
            data: {
                nama_menu: nama_menu,
                link: link
            },
            success: function(res) {
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
                $btn.find('.btn-text').text('Tambahkan');

                if (res.status === 'ok') {
                    $('#menu-list').prepend(`
                        <li class="list-group-item" data-id="${res.data.id_main}" data-type="main" id="menu-${res.data.id_main}">
                            <div class="d-flex justify-content-between align-items-center menu-item">
                                <span><i class="fas fa-bars mx-3"></i> ${nama_menu}</span>
                                <button class="badge badge badge-style-light btn-hapus text-danger" data-id="${res.data.id_main}">
                                    <i class="fas fa-trash-alt"></i> hapus
                                </button>
                            </div>
                            <ul class="list-group ml-4 sortable"></ul>
                        </li>
                    `);
                    $('#notif-custom-menu').html(`
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Menu berhasil ditambahkan.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                    updateMenuCount();
                    initSortable();
                    $('#form-custom-menu')[0].reset(); // reset form
                } else {
                    $('#notif-custom-menu').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${res.error}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    `);
                }
            },
            error: function() {
                $btn.prop('disabled', false);
                $btn.find('.spinner-border').addClass('d-none');
                $btn.find('.btn-text').text('Tambahkan');
                $('#notif-custom-menu').html(`
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal menambahkan menu.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `);
            }
        });
    });
    </script>

    

<?= $this->endSection() ?>