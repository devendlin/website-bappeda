<?php
namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Banner extends BaseController
{
    public function index()
    {
        $data['title'] = 'Dashboard';
        return view('roomloki/banner/banner', $data); // Tampilan hanya layout, data diambil lewat AJAX
    }

    public function getAll()
    {
        $db = \Config\Database::connect();
        $kotak = $db->table('banner')->where('tipe', 'kotak')->orderBy('tgl_posting', 'DESC')->get()->getResult();
        $panjang = $db->table('banner')->where('tipe', 'panjang')->orderBy('tgl_posting', 'DESC')->get()->getResult();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'kotak' => $kotak,
                'panjang' => $panjang
            ]
        ]);
    }

    public function upload()
    {
        $file  = $this->request->getFile('gambar');
        $judul = $this->request->getPost('judul');
        $tipe  = $this->request->getPost('tipe');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gambar tidak valid']);
        }

        $namaFile = $file->getRandomName();
        $file->move(FCPATH . 'uploads/banner/', $namaFile);

        $db = \Config\Database::connect();
        $db->table('banner')->insert([
            'judul'       => $judul,
            'gambar'      => $namaFile,
            'tipe'        => $tipe,
            'url'         => $this->request->getPost('url'),
            'tgl_posting' => date('Y-m-d H:i:s'),
        ]);

        $banner = $db->table('banner')->where('id_banner', $db->insertID())->get()->getRow();

        return $this->response->setJSON(['status' => 'success', 'message' => 'Banner berhasil diupload', 'data' => $banner]);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $banner = $db->table('banner')->where('id_banner', $id)->get()->getRow();
        if ($banner) {
            $path = FCPATH . 'uploads/banner/' . $banner->gambar;
            if (is_file($path)) {
                unlink($path);
            }
            $db->table('banner')->delete(['id_banner' => $id]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Banner berhasil dihapus']);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Banner tidak ditemukan']);
    }
}
