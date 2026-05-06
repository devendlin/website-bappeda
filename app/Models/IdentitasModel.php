<?php
namespace App\Models;

use CodeIgniter\Model;

class IdentitasModel extends Model
{
    protected $table = 'identitas';
    protected $primaryKey = 'id_identitas';
    protected $allowedFields = [
        'nama_website', 'alamat', 'email', 'keywords', 'description', 'favicon', 'logo'
    ];
}
