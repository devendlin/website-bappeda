<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\PpidKategoriModel;
use App\Models\PpidDokumenModel;

class Ppid extends BaseController
{
    public function index()
    {
        $kategoriModel = new PpidKategoriModel();
        
        $data = [
            'title'    => 'PPID - Publikasi Dokumen',
            'categories' => $kategoriModel->getWithCount()
        ];

        return view('frontend/ppid/index', $data);
    }

    public function detail($slug)
    {
        $kategoriModel = new PpidKategoriModel();
        $dokumenModel = new PpidDokumenModel();

        $kategori = $kategoriModel->where('slug_kategori', $slug)->first();
        
        if (!$kategori) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title'    => 'Dokumen ' . $kategori['nama_kategori'],
            'kategori' => $kategori,
            'dokumen'  => $dokumenModel->where('id_kategori', $kategori['id_kategori'])
                                      ->orderBy('tgl_upload', 'DESC')
                                      ->findAll()
        ];

        return view('frontend/ppid/detail', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');
        $dokumenModel = new PpidDokumenModel();

        $data = [
            'title'    => 'Pencarian Dokumen: ' . esc($keyword),
            'keyword'  => $keyword,
            'dokumen'  => $dokumenModel->getSearch($keyword)
        ];

        return view('frontend/ppid/search', $data);
    }

    public function trackView($id)
    {
        $model = new PpidDokumenModel();
        $model->where('id_dokumen', $id)->set('views', 'views+1', false)->update();
        return $this->response->setJSON(['status' => 'ok']);
    }

    public function trackDownload($id)
    {
        $model = new PpidDokumenModel();
        $model->where('id_dokumen', $id)->set('downloads', 'downloads+1', false)->update();
        return $this->response->setJSON(['status' => 'ok']);
    }
}
