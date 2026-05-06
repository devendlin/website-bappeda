<?php

use CodeIgniter\Router\RouteCollection;
use CodeIgniter\Exceptions\PageNotFoundException;
/**
 * @var RouteCollection $routes
 */

// WAJIB: Matikan Auto Routing legacy/manual
$routes->setAutoRoute(false);


// Berita: gunakan jalur detail dan pagination secara eksplisit
$routes->get('/', 'Frontend\Home::index');
$routes->get('berita', 'Frontend\Berita::index');
$routes->get('berita/detail', 'Frontend\Berita::index');
$routes->get('berita/loadMore', 'Frontend\Berita::loadMore');
$routes->get('berita/(:num)', 'Frontend\Berita::index/$1');
$routes->get('berita/detail/(:segment)', 'Frontend\Berita::detail/$1');

// Hindari bentrok: HAPUS ini jika pakai route "berita/detail"
# $routes->get('berita/(:segment)', 'Frontend\Berita::detail/$1');

$routes->get('page/(:segment)', 'Frontend\Berita::halaman/$1');
$routes->get('kategori', 'Frontend\Berita::kategori_all');
$routes->get('kategori/(:segment)', 'Frontend\Berita::kategori/$1');
$routes->get('kategori/(:segment)/(:num)', 'Frontend\Berita::kategori/$1/$2');
$routes->get('dokumentasi', 'Frontend\Dokumentasi::index');
$routes->get('dokumentasi/loadMore', 'Frontend\Dokumentasi::loadMore');
$routes->get('ppid', 'Frontend\Ppid::index');
$routes->get('ppid/search', 'Frontend\Ppid::search');
$routes->get('ppid/track-view/(:num)', 'Frontend\Ppid::trackView/$1');
$routes->get('ppid/track-download/(:num)', 'Frontend\Ppid::trackDownload/$1');
$routes->get('agenda', 'Frontend\Agenda::index');
$routes->get('search', 'Frontend\Search::index');
$routes->post('chat-ai/ask', 'Frontend\ChatAi::ask');
$routes->get('chat-ai/assistants', 'Frontend\ChatAi::getAssistants');
$routes->get('ppid/(:segment)', 'Frontend\Ppid::detail/$1');

// BACKEND
$routes->group('roomloki', function($routes) {

    $routes->get('/', 'Roomloki\Auth::index');
    $routes->post('auth', 'Roomloki\Auth::login');
    $routes->get('logout', 'Roomloki\Auth::logout');

    $routes->group('', ['filter' => 'auth'], function($routes) {
        $routes->get('dashboard', 'Roomloki\Dashboard::index');

        $routes->group('', ['filter' => 'role:superadmin'], function($routes) {
            $routes->get('menu', 'Roomloki\Menu::index');
            $routes->post('menu/tambahkan_kategori', 'Roomloki\Menu::tambahkan_kategori');
            $routes->post('menu/tambahkan_halaman', 'Roomloki\Menu::tambahkan_halaman');
            $routes->post('menu/tambahkan_custom_menu', 'Roomloki\Menu::tambahkan_custom_menu');
            $routes->post('menu/simpan_urutan', 'Roomloki\Menu::simpan_urutan');
            $routes->delete('menu/hapus/(:num)', 'Roomloki\Menu::hapus/$1');

            $routes->get('identitas_web', 'Roomloki\IdentitasWeb::index');
            $routes->post('identitas_web/update', 'Roomloki\IdentitasWeb::update');

            $routes->get('users', 'Roomloki\Users::index');
            $routes->get('users/ajax', 'Roomloki\Users::ajax');
            $routes->post('users/get', 'Roomloki\Users::get');
            $routes->post('users/save', 'Roomloki\Users::save');
            $routes->delete('users/delete/(:segment)', 'Roomloki\Users::delete/$1');

            $routes->get('halaman', 'Roomloki\Halaman::index');
            $routes->post('halaman/ajaxList', 'Roomloki\Halaman::ajaxList');
            $routes->get('halaman/tambah', 'Roomloki\Halaman::tambah');
            $routes->post('halaman/upload_image', 'Roomloki\Halaman::upload_image');
            $routes->post('halaman/simpan', 'Roomloki\Halaman::simpan');
            $routes->get('halaman/image_list', 'Roomloki\Halaman::image_list');
            $routes->get('halaman/edit/(:num)', 'Roomloki\Halaman::edit/$1');
            $routes->post('halaman/update/(:num)', 'Roomloki\Halaman::update/$1');
            $routes->delete('halaman/hapus/(:num)', 'Roomloki\Halaman::hapus/$1');

            // Aplikasi Management
            $routes->get('aplikasi', 'Roomloki\Aplikasi::index');
            $routes->post('aplikasi/ajaxList', 'Roomloki\Aplikasi::ajaxList');
            $routes->post('aplikasi/simpan', 'Roomloki\Aplikasi::simpan');
            $routes->delete('aplikasi/hapus/(:num)', 'Roomloki\Aplikasi::hapus/$1');
        });

        $routes->group('', ['filter' => 'role:admin,superadmin'], function($routes) {
            $routes->get('berita', 'Roomloki\Berita::index');
            $routes->post('berita/ajaxList', 'Roomloki\Berita::ajaxList');
            $routes->get('berita/tambah', 'Roomloki\Berita::tambah');
            $routes->post('berita/upload_image', 'Roomloki\Berita::upload_image');
            $routes->post('berita/simpan', 'Roomloki\Berita::simpan');
            $routes->get('berita/image_list', 'Roomloki\Berita::image_list');
            $routes->get('berita/edit/(:num)', 'Roomloki\Berita::edit/$1');
            $routes->post('berita/update/(:num)', 'Roomloki\Berita::update/$1');
            $routes->delete('berita/hapus/(:num)', 'Roomloki\Berita::hapus/$1');
            $routes->delete('berita/hapus_gambar', 'Roomloki\Berita::hapus_gambar');

            $routes->get('kategori', 'Roomloki\Kategori::index');
            $routes->post('kategori/ajaxList', 'Roomloki\Kategori::ajaxList');
            $routes->post('kategori/simpan', 'Roomloki\Kategori::simpan');
            $routes->delete('kategori/hapus/(:num)', 'Roomloki\Kategori::hapus/$1');

            $routes->get('tags', 'Roomloki\Tags::index');
            $routes->post('tags/ajaxList', 'Roomloki\Tags::ajaxList');
            $routes->post('tags/simpan', 'Roomloki\Tags::simpan');
            $routes->delete('tags/hapus/(:num)', 'Roomloki\Tags::hapus/$1');

            $routes->get('galeri', 'Roomloki\Galeri::index');
            $routes->get('galeri/loadMoreGaleri', 'Roomloki\Galeri::loadMoreGaleri');
            $routes->post('galeri/upload', 'Roomloki\Galeri::upload');
            $routes->post('galeri/hapus_semua_unused', 'Roomloki\Galeri::hapus_semua_unused');

            $routes->get('banner', 'Roomloki\Banner::index');
            $routes->post('banner/upload', 'Roomloki\Banner::upload');
            $routes->get('banner/getAll', 'Roomloki\Banner::getAll');
            $routes->delete('banner/delete/(:num)', 'Roomloki\Banner::delete/$1');

            $routes->get('logadmin', 'Roomloki\LogAdmin::index');
            $routes->post('logadmin/ajaxList', 'Roomloki\LogAdmin::ajaxList');

            $routes->get('profil', 'Roomloki\Profil::index');
            $routes->post('profil/updateProfil', 'Roomloki\Profil::updateProfil');

            // PPID Management
            $routes->group('ppid', function($routes) {
                $routes->group('kategori', function($routes) {
                    $routes->get('/', 'Roomloki\PpidKategori::index');
                    $routes->post('ajaxList', 'Roomloki\PpidKategori::ajaxList');
                    $routes->get('tambah', 'Roomloki\PpidKategori::tambah');
                    $routes->post('simpan', 'Roomloki\PpidKategori::simpan');
                    $routes->get('edit/(:num)', 'Roomloki\PpidKategori::edit/$1');
                    $routes->post('update/(:num)', 'Roomloki\PpidKategori::update/$1');
                    $routes->delete('hapus/(:num)', 'Roomloki\PpidKategori::hapus/$1');
                });
                $routes->group('dokumen', function($routes) {
                    $routes->get('/', 'Roomloki\PpidDokumen::index');
                    $routes->get('(:num)', 'Roomloki\PpidDokumen::index/$1');
                    $routes->post('ajaxList', 'Roomloki\PpidDokumen::ajaxList');
                    $routes->post('ajaxList/(:num)', 'Roomloki\PpidDokumen::ajaxList/$1');
                    $routes->get('tambah', 'Roomloki\PpidDokumen::tambah');
                    $routes->get('tambah/(:num)', 'Roomloki\PpidDokumen::tambah/$1');
                    $routes->post('simpan', 'Roomloki\PpidDokumen::simpan');
                    $routes->get('edit/(:num)', 'Roomloki\PpidDokumen::edit/$1');
                    $routes->post('update/(:num)', 'Roomloki\PpidDokumen::update/$1');
                    $routes->delete('hapus/(:num)', 'Roomloki\PpidDokumen::hapus/$1');
                });
            });
            // Dokumentasi Kegiatan Management
            $routes->group('dokumentasi', function($routes) {
                $routes->get('/', 'Roomloki\Dokumentasi::index');
                $routes->post('ajaxList', 'Roomloki\Dokumentasi::ajaxList');
                $routes->get('tambah', 'Roomloki\Dokumentasi::tambah');
                $routes->post('simpan', 'Roomloki\Dokumentasi::simpan');
                $routes->get('edit/(:num)', 'Roomloki\Dokumentasi::edit/$1');
                $routes->post('update/(:num)', 'Roomloki\Dokumentasi::update/$1');
                $routes->delete('hapusFoto/(:num)', 'Roomloki\Dokumentasi::hapusFoto/$1');
                $routes->delete('hapus/(:num)', 'Roomloki\Dokumentasi::hapus/$1');
            });

            // Agenda Kegiatan Management
            $routes->group('agenda', function($routes) {
                $routes->get('/', 'Roomloki\Agenda::index');
                $routes->post('ajaxList', 'Roomloki\Agenda::ajaxList');
                $routes->get('tambah', 'Roomloki\Agenda::tambah');
                $routes->post('simpan', 'Roomloki\Agenda::simpan');
                $routes->get('edit/(:num)', 'Roomloki\Agenda::edit/$1');
                $routes->post('update/(:num)', 'Roomloki\Agenda::update/$1');
                $routes->delete('hapus/(:num)', 'Roomloki\Agenda::hapus/$1');
            });
        });
    });
});

// Custom 404 hanya untuk frontend
$routes->set404Override(function () {
    $uri = uri_string();

    if (!str_starts_with($uri, 'roomloki')) {
        return view('errors/html/custom_404', [
            'title' => '404 - Halaman tidak ditemukan.',
            'message' => 'Halaman yang kamu cari tidak ditemukan.'
        ]);
    }

    // lempar ke handler bawaan CI (jangan panggil render langsung)
    throw new PageNotFoundException("Halaman $uri tidak ditemukan.");
});
