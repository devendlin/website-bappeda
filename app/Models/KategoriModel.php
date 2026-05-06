<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table            = 'kategori';
    protected $primaryKey       = 'id_kategori';
    protected $allowedFields    = ['nama_kategori', 'kategori_seo'];
    protected $returnType       = 'array';

    public function getAll()
    {
        return $this->select('id_kategori, nama_kategori, kategori_seo')->findAll();
    }
}
