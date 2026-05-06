<?php

namespace App\Cells;

use CodeIgniter\View\Cells\Cell;
use App\Models\BeritaModel;
use App\Models\MenuModel;

class HeaderCell extends Cell
{
    public function display(): string
    {
        // Ambil 5 berita terbaru
        $beritaModel = new BeritaModel();
        $data['runTextBerita'] = $beritaModel
            ->orderBy('id_berita', 'DESC') // atau 'tanggal' tergantung struktur
            ->limit(5)
            ->find();

        // Ambil Kategori Unutk Menu
        $menuModel = new MenuModel();
        $data['menu'] = $menuModel->getAllWithSub();
        return view('frontend/layout/header', $data);
    }
    
}
