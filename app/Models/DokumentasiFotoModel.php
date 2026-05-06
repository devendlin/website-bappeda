<?php

namespace App\Models;

use CodeIgniter\Model;

class DokumentasiFotoModel extends Model
{
    protected $table            = 'dokumentasi_foto';
    protected $primaryKey       = 'id_foto';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_dokumentasi', 'file_foto'];
}
