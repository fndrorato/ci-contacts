<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAddressTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_contact' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'zip_code' => ['type' => 'VARCHAR', 'constraint' => 20],
            'country' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'state' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'street_address' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'address_number' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'city' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'address_line' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'neighborhood' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_contact', 'contacts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('address');
    }

    public function down()
    {
        $this->forge->dropTable('address');
    }
}
