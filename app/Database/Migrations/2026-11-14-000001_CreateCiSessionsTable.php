<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCiSessionsTable extends Migration
{
    public function up(): void
    {
        if ($this->db->DBDriver === 'Postgre') {
            $this->db->query(
                'CREATE TABLE IF NOT EXISTS "ci_sessions" (
                    "id" varchar(128) NOT NULL,
                    "ip_address" varchar(45) NOT NULL,
                    "timestamp" timestamptz DEFAULT CURRENT_TIMESTAMP NOT NULL,
                    "data" bytea NOT NULL,
                    CONSTRAINT "ci_sessions_pkey" PRIMARY KEY ("id")
                )'
            );
            $this->db->query('CREATE INDEX IF NOT EXISTS "ci_sessions_timestamp" ON "ci_sessions" ("timestamp")');

            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'VARCHAR', 'constraint' => 128, 'null' => false],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => false],
            'timestamp'  => ['type' => 'TIMESTAMP', 'null' => false],
            'data'       => ['type' => 'BLOB', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('timestamp');
        $this->forge->createTable('ci_sessions', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('ci_sessions', true);
    }
}
