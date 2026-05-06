<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\MenuModel;
use App\Models\SubmenuModel;
use App\Models\KategoriModel;
use App\Models\HalamanModel;
class Menu extends BaseController
{
    public function index()
    {
        $data['title'] = "Daftar Menu";
        $menuModel = new MenuModel();
        $kategoriModel = new KategoriModel();
        $halamanModel = new HalamanModel();
        $data['menu'] = $menuModel->getAllWithSub();
        $data['kategori'] = $kategoriModel->getAll();
        $data['halaman'] = $halamanModel->getAll();
        return view('roomloki/menu/menu', $data);
    }
    public function tambahkan_kategori()
    {
        
        $kategori_seo = $this->request->getPost('kategori_seo');
        $nama_kategori = $this->request->getPost('nama_kategori');

        $data = [
            'nama_menu' => $nama_kategori,
            'link'      => 'kategori/' . $kategori_seo
        ];

        $menuModel = new MenuModel();
        $menuModel->insert($data);
        $id = $menuModel->getInsertID();

        return $this->response->setJSON([
            'status' => 'ok',
            'csrf_token' => csrf_hash(),
            'data' => [
                'id_main' => $id,
                'nama_menu' => $nama_kategori,
            ]
        ]);
    }
    public function tambahkan_halaman()
    {
        
        $halaman_seo = $this->request->getPost('halaman_seo');
        $nama_halaman = $this->request->getPost('nama_halaman');

        $data = [
            'nama_menu' => $nama_halaman,
            'link'      => 'page/' . $halaman_seo
        ];

        $menuModel = new MenuModel();
        $menuModel->insert($data);
        $id = $menuModel->getInsertID();

        return $this->response->setJSON([
            'status' => 'ok',
            'csrf_token' => csrf_hash(),
            'data' => [
                'id_main' => $id,
                'nama_menu' => $nama_halaman,
            ]
        ]);
    }

    public function tambahkan_custom_menu()
    {
        
        $nama_menu = $this->request->getPost('nama_menu');
        $link = $this->request->getPost('link');

        $data = [
            'nama_menu' => $nama_menu,
            'link'      => $link
        ];

        $menuModel = new MenuModel();
        $menuModel->insert($data);
        $id = $menuModel->getInsertID();

        return $this->response->setJSON([
            'status' => 'ok',
            'csrf_token' => csrf_hash(),
            'data' => [
                'id_main' => $id,
                'nama_menu' => $nama_menu,
            ]
        ]);
    }

    public function hapus($id)
    {
        $menuModel = new MenuModel();
        if ($menuModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'ok',
                'csrf_token' => csrf_hash()
            ]);
        } else {
            return $this->response->setStatusCode(500)->setJSON(['status' => 'error']);
        }
    }

public function simpan_urutan()
{
    $json = $this->request->getPost('data');
    $items = json_decode($json, true);

    if (!is_array($items)) {
        return $this->response->setStatusCode(400)->setJSON(['status' => 'fail', 'message' => 'Data tidak valid']);
    }

    $this->prosesNestedMenu($items, null);

    return $this->response->setJSON(['status' => 'ok']);
}

private function prosesNestedMenu($items, $parentId = null)
{
    $menuModel    = new \App\Models\MenuModel();     // Model untuk main menu
    $submenuModel = new \App\Models\SubmenuModel();  // Model untuk submenu

    foreach ($items as $item) {
        $id     = $item['id'];
        $type   = $item['type'];
        $urutan = $item['urutan'];

        log_message('debug', 'Proses item: ' . json_encode([
            'id'       => $id,
            'type'     => $type,
            'urutan'   => $urutan,
            'parentId' => $parentId,
        ]));

        if ($type === 'main') {
            // Jika ternyata data ini adalah sub sebelumnya → pindahkan ke main
            $sub = $submenuModel->find($id);
            if ($sub) {
                log_message('debug', "SUB jadi MAIN, hapus sub id_sub = $id");
                $submenuModel->delete($id);

                $menuModel->insert([
                    'nama_menu' => $sub['nama_sub'],
                    'link'      => $sub['link_sub'],
                    'urutan'    => $urutan,
                    'aktif'     => 'Y'
                ]);

                $newId = $menuModel->getInsertID();
                log_message('debug', "Insert ke MAIN baru, id_main = $newId");
            } else {
                // Jika memang main, update urutan saja
                $menuModel->update($id, ['urutan' => $urutan]);
                log_message('debug', "Update urutan MAIN id_main = $id");
                $newId = $id;
            }

            // Proses anak-anak submenu
            if (!empty($item['children'])) {
                $this->prosesNestedMenu($item['children'], $newId);
            }
        }

        if ($type === 'sub') {
            // Jika ternyata data ini adalah main sebelumnya → pindahkan ke sub
            $main = $menuModel->find($id);
            if ($main) {
                log_message('debug', "MAIN jadi SUB, hapus main id_main = $id");
                $menuModel->delete($id);

                // Jika ada submenu dengan ID sama, hapus dulu (bentrok ID)
                if ($submenuModel->find($id)) {
                    $submenuModel->delete($id);
                    log_message('debug', "Hapus bentrok id_sub = $id");
                }

                $submenuModel->insert([
                    'id_sub'    => $id,
                    'nama_sub'  => $main['nama_menu'],
                    'link_sub'  => $main['link'],
                    'id_main'   => $parentId,
                    'urutan'    => $urutan,
                    'aktif'     => 'Y'
                ]);

                log_message('debug', "Insert ke SUB id_sub = $id, parent = $parentId");
            } else {
                // Update posisi dan parent ID
                $submenuModel->update($id, [
                    'id_main' => $parentId,
                    'urutan'  => $urutan
                ]);
                log_message('debug', "Update SUB id_sub = $id, parent = $parentId");
            }
        }
    }
}




}
