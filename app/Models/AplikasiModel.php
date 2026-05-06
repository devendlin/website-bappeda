<?php

namespace App\Models;

use CodeIgniter\Model;

class AplikasiModel extends Model
{
    protected $table            = 'aplikasi';
    protected $primaryKey       = 'id_aplikasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_aplikasi', 'url', 'gambar', 'urutan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getAll()
    {
        return $this->orderBy('urutan', 'ASC')->findAll();
    }
}
