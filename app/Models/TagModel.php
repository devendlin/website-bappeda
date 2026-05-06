<?php

namespace App\Models;

use CodeIgniter\Model;

class TagModel extends Model
{
    protected $table            = 'tag';
    protected $primaryKey       = 'id_tag';
    protected $allowedFields    = ['nama_tag','tag_seo'];
    protected $returnType       = 'array';
    
    public function getTag()
    {
        return $this->select('id_tag, nama_tag, tag_seo')->findAll();
    }
}
