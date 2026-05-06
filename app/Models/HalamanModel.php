<?php

namespace App\Models;

use CodeIgniter\Model;

class HalamanModel extends Model
{
    protected $table            = 'halaman';
    protected $primaryKey       = 'id_halaman';
    protected $allowedFields    = [
                                    'judul',
                                    'judul_seo',
                                    'isi_halaman',
                                    'gambar',
                                    'tanggal',
                                    'created_at'
                                ];
    protected $returnType       = 'array';

    public function getAll()
    {
        return $this->select('id_halaman, judul, judul_seo')->findAll();
    }
}
