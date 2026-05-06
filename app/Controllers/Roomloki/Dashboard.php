<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    public function index()
    {
        helper('tanggal');
        $data['title'] = 'Dashboard';
        $db = \Config\Database::connect();
        $builder = $db->table('visitor_logs');

        $query = $builder->select("tanggal, COUNT(*) as jumlah")
            ->where('tanggal >=', date('Y-m-d', strtotime('-6 days')))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->get();

        $hasil = $query->getResult();

        // Format untuk chart
        $data['labels'] = [];
        $data['jumlah'] = [];

        $total = 0;
        foreach ($hasil as $row) {
            $data['labels'][] = formatTanggalIndo($row->tanggal);
            $data['jumlah'][] = $row->jumlah;
            $total += $row->jumlah;
        }
        $data['total_pengunjung'] = $total;
        return view('roomloki/dashboard', $data);
    }

}
