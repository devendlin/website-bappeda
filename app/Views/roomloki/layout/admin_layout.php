<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title>Siberkreasimu - <?=$title?></title>

    <link rel="shortcut icon" href="<?= $meta['favicon'] ?>" />
    <link rel="apple-touch-icon" href="<?= $meta['favicon'] ?>" />
    <link rel="icon" type="image/png" href="<?= $meta['favicon'] ?>" />

    <!-- Custom fonts for this template-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    
    <!-- Custom styles for this template-->
    <link href="<?=base_url()?>backend/css/sb-admin-2.css" rel="stylesheet">
    <link href="<?=base_url()?>backend/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- include summernote css/js -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs4.min.css" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.default.min.css"
        integrity="sha512-pTaEn+6gF1IeWv3W1+7X7eM60TFu/agjgoHmYhAfLEU8Phuf6JKiiE8YmsNC0aCgQv4192s4Vai8YZ6VNM6vyQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"/>
        <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
    .list-group{
        min-height:10px;
    }
</style>
</head>

<body id="page-top">
    
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('roomloki') ?>">
                <div class="sidebar-brand-icon rotate-n-15 text-warning">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3" style="font-size:65%"><?= $identitas['nama_website'] ?></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <?php $role = session()->get('role'); ?>

            <!-- Nav Item - Dashboard -->
            <li class="nav-item <?= url_is('roomloki/dashboard') ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('roomloki/dashboard') ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <hr class="sidebar-divider">

            <!-- Bagian untuk Superadmin -->
            <?php if ($role == 'superadmin'): ?>
                <div class="sidebar-heading">Setting</div>

                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSetting" aria-expanded="true" aria-controls="collapseSetting">
                        <i class="fas fa-fw fa-wrench"></i>
                        <span>Setting</span>
                    </a>
                    <div id="collapseSetting" class="collapse" data-parent="#accordionSidebar">
                        <div class="bg-white-transparent py-2 collapse-inner rounded">
                            <a class="collapse-item" href="<?= base_url('roomloki/menu') ?>">Menu Utama</a>
                            <a class="collapse-item" href="<?= base_url('roomloki/identitas_web') ?>">Identitas Web</a>
                            <a class="collapse-item" href="<?= base_url('roomloki/users') ?>">Manajemen User</a>
                        </div>
                    </div>
                </li>

                <hr class="sidebar-divider">
            <?php endif; ?>

            <!-- Bagian untuk Superadmin & Admin -->
            <?php if (in_array($role, ['admin', 'superadmin'])): ?>
                <div class="sidebar-heading">Content</div>

                <li class="nav-item <?= url_is('roomloki/berita*') || url_is('roomloki/halaman*') || url_is('roomloki/kategori*') || url_is('roomloki/tags*') ? 'active' : '' ?>">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaman" aria-expanded="true" aria-controls="collapseLaman">
                        <i class="fas fa-fw fa-folder"></i>
                        <span>Laman Berita</span>
                    </a>
                    <div id="collapseLaman" class="collapse" data-parent="#accordionSidebar">
                        <div class="bg-white-transparent py-2 collapse-inner rounded">
                            <a class="collapse-item <?= url_is('roomloki/berita*') ? 'active' : '' ?>" href="<?= base_url('roomloki/berita') ?>">Berita</a>
                            <?php if ($role == 'superadmin'): ?>
                                <a class="collapse-item <?= url_is('roomloki/halaman*') ? 'active' : '' ?>" href="<?= base_url('roomloki/halaman') ?>">Halaman Statis</a>
                                <div class="collapse-divider"></div>
                            <?php endif; ?>
                            <a class="collapse-item <?= url_is('roomloki/kategori*') ? 'active' : '' ?>" href="<?= base_url('roomloki/kategori') ?>">Kategori Berita</a>
                            <a class="collapse-item <?= url_is('roomloki/tags*') ? 'active' : '' ?>" href="<?= base_url('roomloki/tags') ?>">Tag / Label</a>
                        </div>
                    </div>
                </li>

                <li class="nav-item <?= url_is('roomloki/galeri*') || url_is('roomloki/banner*') ? 'active' : '' ?>">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMedia" aria-expanded="true" aria-controls="collapseMedia">
                        <i class="fas fa-fw fa-photo-video"></i>
                        <span>Media</span>
                    </a>
                    <div id="collapseMedia" class="collapse" data-parent="#accordionSidebar">
                        <div class="bg-white-transparent py-2 collapse-inner rounded">
                            <a class="collapse-item <?= url_is('roomloki/galeri*') ? 'active' : '' ?>" href="<?= base_url('roomloki/galeri') ?>">Galeri</a>
                            <a class="collapse-item <?= url_is('roomloki/banner*') ? 'active' : '' ?>" href="<?= base_url('roomloki/banner') ?>">Banner</a>
                            <?php if ($role == 'superadmin'): ?>
                                <a class="collapse-item <?= url_is('roomloki/aplikasi*') ? 'active' : '' ?>" href="<?= base_url('roomloki/aplikasi') ?>">Aplikasi</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>

                <li class="nav-item <?= strpos(current_url(), 'roomloki/ppid') !== false ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('roomloki/ppid/kategori') ?>">
                        <i class="fas fa-fw fa-file-pdf"></i>
                        <span>PPID & Dokumen</span>
                    </a>
                </li>

                <li class="nav-item <?= url_is('roomloki/dokumentasi*') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('roomloki/dokumentasi') ?>">
                        <i class="fas fa-fw fa-camera-retro"></i>
                        <span>Dokumentasi Kegiatan</span>
                    </a>
                </li>

                <li class="nav-item <?= url_is('roomloki/agenda*') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('roomloki/agenda') ?>">
                        <i class="fas fa-fw fa-calendar-check"></i>
                        <span>Agenda Kegiatan</span>
                    </a>
                </li>

                <li class="nav-item <?= url_is('roomloki/logadmin*') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('roomloki/logadmin') ?>">
                        <i class="fas fa-fw fa-list"></i>
                        <span>Activity Log</span></a>
                </li>
                <li class="nav-item <?= url_is('roomloki/profil*') ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('roomloki/profil') ?>">
                        <i class="fas fa-fw fa-user"></i>
                        <span>Profile</span></a>
                </li>

                <hr class="sidebar-divider">
            <?php endif; ?>

            

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600"><?= session()->get('username') ?></span>
                                <img class="img-profile rounded-circle"
                                    src="<?=base_url()?>backend/img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="<?= base_url('roomloki/profil') ?>">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="<?=base_url()?>roomloki/logadmin">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="<?=base_url()?>roomloki/logout" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>
                   
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a href="<?=base_url()?>" target="_blank" class="nav-link">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                            
                        </li>

                    </ul>
                    
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <?= $this->renderSection('content') ?>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; 2025</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="<?=base_url()?>roomloki/logout">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="csrf_token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
    <!-- Bootstrap core JavaScript-->
    <script src="<?=base_url()?>backend/vendor/jquery/jquery.min.js"></script>
    <script src="<?=base_url()?>backend/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?=base_url()?>backend/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?=base_url()?>backend/js/sb-admin-2.min.js"></script>
    <!-- Page level plugins -->
    <script src="<?=base_url()?>backend/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="<?=base_url()?>backend/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="<?=base_url()?>backend/js/demo/datatables-demo.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs4.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js"
        integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer">
    </script>
    <?php if (session()->getFlashdata('error_role')) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= session()->getFlashdata('error_role'); ?>',
        });
    </script>
    <?php endif; ?>
    
    <script>
    const csrfName = document.querySelector('meta[name="csrf-name"]').content;
    let csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Set global headers AJAX (Standar paling aman agar tidak merusak FormData)
    $.ajaxSetup({
        headers: {
            '<?= config('Security')->headerName ?>': csrfToken
        }
    });

    // Saat AJAX sukses, update token dari response (kalau ada)
    $(document).ajaxSuccess(function (event, xhr, settings, response) {
        if (response && response.csrf_token) {
            csrfToken = response.csrf_token;
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', csrfToken);
            
            $.ajaxSetup({
                headers: {
                    '<?= config('Security')->headerName ?>': csrfToken
                }
            });
        }

        // Optional: Reload datatable tertentu
        if (response && response.reloadTabel) {
            const tableId = response.reloadTabel;
            if ($.fn.DataTable.isDataTable('#' + tableId)) {
                $('#' + tableId).DataTable().ajax.reload(null, false);
            }
        }
    });

    function getCSRFData() {
        const name = document.querySelector('meta[name="csrf-name"]').getAttribute('content');
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        return { [name]: token };
    }
    </script>
    <?= $this->renderSection('content_js') ?>
</body>

</html>