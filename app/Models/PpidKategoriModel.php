<?php

namespace App\Models;

use CodeIgniter\Model;

class PpidKategoriModel extends Model
{
    protected $table            = 'ppid_kategori';
    protected $primaryKey       = 'id_kategori';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_kategori', 'slug_kategori', 'deskripsi', 'icon'];

    public function getWithCount()
    {
        return $this->select('ppid_kategori.*, COUNT(ppid_dokumen.id_dokumen) as total_dokumen')
                    ->join('ppid_dokumen', 'ppid_dokumen.id_kategori = ppid_kategori.id_kategori', 'left')
                    ->groupBy('ppid_kategori.id_kategori')
                    ->findAll();
    }
}
