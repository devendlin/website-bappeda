<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;

class Dokumentasi extends BaseController
{
    public function index()
    {
        $limit = 6;
        $rawKegiatan = $this->getKegiatanData($limit, 0);
        $total = (new \App\Models\DokumentasiModel())->countAllResults();

        $data = [
            'title' => 'Dokumentasi Kegiatan',
            'initialKegiatan' => $rawKegiatan,
            'hasMore' => $total > $limit,
            'limit' => $limit
        ];

        return view('frontend/dokumentasi', $data);
    }

    public function loadMore()
    {
        $offset = (int) $this->request->getGet('offset');
        $limit = 6;
        
        $kegiatan = $this->getKegiatanData($limit, $offset);
        $total = (new \App\Models\DokumentasiModel())->countAllResults();

        return $this->response->setJSON([
            'kegiatan' => $kegiatan,
            'hasMore' => $total > ($offset + $limit)
        ]);
    }

    private function getKegiatanData($limit, $offset)
    {
        $model = new \App\Models\DokumentasiModel();
        $fotoModel = new \App\Models\DokumentasiFotoModel();
        
        $raw = $model->orderBy('tanggal', 'DESC')->findAll($limit, $offset);
        $mapped = [];

        foreach ($raw as $item) {
            $fotos = $fotoModel->where('id_dokumentasi', $item['id_dokumentasi'])->findAll();
            $photoUrls = [];
            foreach ($fotos as $f) {
                $photoUrls[] = base_url('uploads/galeri/' . $f['file_foto']);
            }

            $mapped[] = [
                'id' => $item['id_dokumentasi'],
                'judul' => $item['judul'],
                'tanggal' => timeAgoOrDate($item['tanggal']),
                'deskripsi' => $item['deskripsi'],
                'foto' => $photoUrls
            ];
        }
        return $mapped;
    }
}
