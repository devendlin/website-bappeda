<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLogoToIdentitas extends Migration
{
    public function up()
    {
        $fields = [
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
        ];
        $this->forge->addColumn('identitas', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('identitas', 'logo');
    }
}
