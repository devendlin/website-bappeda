<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdUserToBerita extends Migration
{
    public function up()
    {
        $this->forge->addColumn('berita', [
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'id_berita'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('berita', 'id_user');
    }
}
