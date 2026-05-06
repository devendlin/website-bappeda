<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;
use App\Models\BeritaModel;
use App\Models\MenuModel;

class SidebarCell extends Cell
{
    public function render(): string
    {
        helper('timeAgo');
        // Ambil 5 berita terbaru
        $beritaModel = new BeritaModel();
        $data['berita_tranding'] = $beritaModel
            ->select('berita.*, COALESCE(SUM(berita_views.jumlah_view), 0) AS total_view')
            ->join('berita_views', 'berita_views.id_berita = berita.id_berita', 'left')
            ->groupBy('berita.id_berita')
            ->orderBy('total_view', 'DESC')
            ->limit(5)
            ->findAll();

        //Iklan
        $db = \Config\Database::connect();
        $data['banner_kotak'] = $db->table('banner')
        ->where('tipe', 'kotak')
        ->orderBy('tgl_posting', 'DESC')
        ->get()
        ->getResultArray();

        return view('frontend/layout/sidebar', $data);
    }
    
}
