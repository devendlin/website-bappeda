<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmenuModel extends Model
{
    protected $table = 'submenu';
    protected $primaryKey = 'id_sub';
    protected $allowedFields = ['id_sub', 'nama_sub', 'link_sub', 'id_main', 'urutan', 'aktif'];
    public $timestamps = false;
    public $useAutoIncrement = false; // PENTING
}
