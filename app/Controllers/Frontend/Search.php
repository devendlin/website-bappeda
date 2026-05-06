<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\BeritaModel;
use App\Models\PpidDokumenModel;
use App\Models\AgendaModel;
use App\Models\DokumentasiModel;

class Search extends BaseController
{
    public function index()
    {
        $keyword = $this->request->getGet('q');
        $keyword = trim($keyword ?? '');

        if (empty($keyword)) {
            return redirect()->back();
        }

        $data = [
            'title' => 'Hasil Pencarian: ' . esc($keyword),
            'keyword' => $keyword,
            'results' => []
        ];

        $words = array_filter(explode(' ', $keyword));
        if (empty($words)) $words = [$keyword]; // Fallback

        // 1. Search Berita
        $beritaModel = new BeritaModel();
        $beritaModel->groupStart();
        foreach ($words as $word) {
            $beritaModel->groupStart()
                ->like('judul', $word)
                ->orLike('isi_berita', $word)
            ->groupEnd();
        }
        $berita = $beritaModel->groupEnd()
            ->orderBy('tanggal', 'DESC')
            ->limit(10)
            ->find();
        
        foreach ($berita as $item) {
            $data['results'][] = [
                'type' => 'Berita',
                'title' => $item['judul'],
                'desc' => strip_tags(substr($item['isi_berita'], 0, 150)) . '...',
                'date' => timeAgoOrDate($item['tanggal']),
                'link' => base_url('berita/detail/' . $item['judul_seo']),
                'icon' => 'article'
            ];
        }

        // 2. Search PPID
        $ppidModel = new PpidDokumenModel();
        $ppidModel->groupStart();
        foreach ($words as $word) {
            $ppidModel->groupStart()
                ->like('judul_dokumen', $word)
                ->orLike('deskripsi', $word)
            ->groupEnd();
        }
        $ppid = $ppidModel->groupEnd()
            ->orderBy('tgl_upload', 'DESC')
            ->limit(10)
            ->find();

        foreach ($ppid as $item) {
            $data['results'][] = [
                'type' => 'Dokumen PPID',
                'title' => $item['judul_dokumen'],
                'desc' => strip_tags(substr($item['deskripsi'], 0, 150)) . '...',
                'date' => timeAgoOrDate($item['tgl_upload']),
                'link' => base_url('ppid/search?q=' . urlencode($item['judul_dokumen'])),
                'icon' => 'description'
            ];
        }

        // 3. Search Agenda
        $agendaModel = new AgendaModel();
        $agendaModel->groupStart();
        foreach ($words as $word) {
            $agendaModel->groupStart()
                ->like('judul', $word)
                ->orLike('deskripsi', $word)
                ->orLike('lokasi', $word)
            ->groupEnd();
        }
        $agenda = $agendaModel->groupEnd()
            ->orderBy('tgl_pelaksanaan', 'DESC')
            ->limit(5)
            ->find();

        foreach ($agenda as $item) {
            $data['results'][] = [
                'type' => 'Agenda',
                'title' => $item['judul'],
                'desc' => 'Lokasi: ' . $item['lokasi'] . '. ' . strip_tags(substr($item['deskripsi'], 0, 100)) . '...',
                'date' => timeAgoOrDate($item['tgl_pelaksanaan']),
                'link' => base_url('agenda'),
                'icon' => 'event'
            ];
        }

        // 4. Search Dokumentasi
        $dokModel = new DokumentasiModel();
        $dokModel->groupStart();
        foreach ($words as $word) {
            $dokModel->groupStart()
                ->like('judul', $word)
                ->orLike('deskripsi', $word)
            ->groupEnd();
        }
        $dok = $dokModel->groupEnd()
            ->orderBy('tanggal', 'DESC')
            ->limit(5)
            ->find();

        foreach ($dok as $item) {
            $data['results'][] = [
                'type' => 'Dokumentasi',
                'title' => $item['judul'],
                'desc' => strip_tags(substr($item['deskripsi'], 0, 100)) . '...',
                'date' => timeAgoOrDate($item['tanggal']),
                'link' => base_url('dokumentasi?id=' . $item['id_dokumentasi']),
                'icon' => 'photo_camera'
            ];
        }

        return view('frontend/search', $data);
    }
}
