<?php

namespace App\Models;

use CodeIgniter\Model;

class PpidDokumenModel extends Model
{
    protected $table            = 'ppid_dokumen';
    protected $primaryKey       = 'id_dokumen';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_kategori', 'judul_dokumen', 'deskripsi', 'file_pdf', 'tgl_upload', 'views', 'downloads'];

    public function getSearch($keyword)
    {
        $builder = $this->select('ppid_dokumen.*, ppid_kategori.nama_kategori')
                    ->join('ppid_kategori', 'ppid_kategori.id_kategori = ppid_dokumen.id_kategori');

        $words = array_filter(explode(' ', trim($keyword)));
        
        if (!empty($words)) {
            $builder->groupStart();
            foreach ($words as $word) {
                $builder->groupStart()
                        ->like('judul_dokumen', $word)
                        ->orLike('ppid_dokumen.deskripsi', $word)
                        ->groupEnd();
            }
            $builder->groupEnd();
        } else {
             // If keyword is empty/spaces only, maybe return all or nothing? 
             // Existing behavior implies checking for empty earlier or returning all.
             // To be safe with empty array, do nothing (returns all).
        }

        return $builder->orderBy('tgl_upload', 'DESC')->findAll();
    }
}
