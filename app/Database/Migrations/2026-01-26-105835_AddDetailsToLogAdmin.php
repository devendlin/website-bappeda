<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailsToLogAdmin extends Migration
{
    public function up()
    {
        $this->forge->addColumn('log_admin', [
            'method' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'after'      => 'url'
            ],
            'aksi' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'method'
            ],
            'data_payload' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'aksi'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('log_admin', ['method', 'aksi', 'data_payload']);
    }
}
