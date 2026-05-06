<?php

namespace App\Controllers\Frontend;

use App\Controllers\BaseController;
use App\Models\AgendaModel;

class Agenda extends BaseController
{
    public function index()
    {
        $model = new AgendaModel();
        
        // Ambil semua agenda yang aktif, urutkan dari yang terbaru (tanggal pelaksanaan)
        $agendas = $model->where('is_active', 1)
                         ->orderBy('tgl_pelaksanaan', 'DESC')
                         ->findAll();

        // Format tanggal untuk tiap agenda
        foreach ($agendas as &$a) {
            $a['tanggal_format'] = !empty($a['tgl_pelaksanaan']) ? date('d F Y', strtotime($a['tgl_pelaksanaan'])) : 'Belum ditentukan';
        }

        $data = [
            'title'   => 'Agenda Kegiatan',
            'agendas' => $agendas
        ];

        return view('frontend/agenda', $data);
    }
}
