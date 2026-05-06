<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumentasiModel extends Model
{
    protected $table            = 'dokumentasi';
    protected $primaryKey       = 'id_dokumentasi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['judul', 'tanggal', 'deskripsi'];

    public function getWithPhotos()
    {
        $data = $this->orderBy('tanggal', 'DESC')->findAll();
        $fotoModel = new DokumentasiFotoModel();
        
        foreach ($data as &$item) {
            $item['foto'] = $fotoModel->where('id_dokumentasi', $item['id_dokumentasi'])->findAll();
        }
        
        return $data;
    }
}
