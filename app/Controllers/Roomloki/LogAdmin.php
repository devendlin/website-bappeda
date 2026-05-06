<?php

namespace App\Controllers\Roomloki;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Hermawan\DataTables\DataTable;

class LogAdmin extends BaseController
{
    public function index()
    {
        $data['title'] = "Actfivity Log";
        return view('roomloki/log_admin/index', $data);
    }

    public function ajaxList()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('log_admin')
            ->select('id_log, id_user, username, url, method, aksi, data_payload, ip_address, user_agent, jumlah_akses, waktu');

        $id_user = session()->get('id_user');
        $role = session()->get('role');

        if ($role !== 'superadmin') {
            $builder->where('id_user', $id_user);
        }

        return DataTable::of($builder)->toJson(true);
    }

}
