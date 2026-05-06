<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUsernameFromBerita extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('username', 'berita')) {
            $this->forge->dropColumn('berita', 'username');
        }
    }

    public function down()
    {
        $this->forge->addColumn('berita', [
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
        ]);
    }
}
