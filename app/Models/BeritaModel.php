<?php

namespace App\Models;

use CodeIgniter\Model;

class BeritaModel extends Model
{
    protected $table            = 'berita';
    protected $primaryKey       = 'id_berita';
    protected $allowedFields    = [
                                    'judul',
                                    'judul_seo',
                                    'isi_berita',
                                    'gambar',
                                    'tanggal',
                                    'created_at',
                                    'tag',
                                    'id_kategori',
                                    'id_user'
                                ];
    protected $returnType       = 'array';
}
