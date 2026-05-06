<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
        protected $table = 'mainmenu';
        protected $primaryKey = 'id_main';
        protected $allowedFields = ['id_main', 'nama_menu', 'link', 'urutan', 'aktif'];
        public $timestamps = false;
        public $useAutoIncrement = true; // PENTING
    
    public function getAll()
    {
        return $this->select('id_main, nama_menu, link, urutan')
                    ->orderBy('urutan', 'ASC')
                    ->findAll();
    }

    public function getAllWithSub()
    {
        $main = $this->db->table('mainmenu')->orderBy('urutan', 'ASC')->get()->getResultArray();

        // Tambahkan orderBy untuk urutan submenu
        $sub = $this->db->table('submenu')
            ->where('aktif', 'Y')
            ->orderBy('id_main', 'ASC') // Optional tapi rapi
            ->orderBy('urutan', 'ASC')  // ✅ Ini kunci agar submenu urut
            ->get()->getResultArray();

        // Gabungkan submenu ke main menu
        foreach ($main as &$m) {
            $m['submenu'] = array_values(array_filter($sub, fn($s) => $s['id_main'] == $m['id_main']));
        }

        return $main;
    }
}
