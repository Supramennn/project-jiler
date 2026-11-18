<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDashboardIndexes extends Migration
{
    public function up(): void
    {
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_orders_status_paid_at ON orders (status, paid_at)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_orders_status_email_status ON orders (status, status_email)');
        $this->db->query('CREATE INDEX IF NOT EXISTS idx_orders_created_at_status ON orders (created_at, status)');
    }

    public function down(): void
    {
        $this->db->query('DROP INDEX IF EXISTS idx_orders_status_paid_at');
        $this->db->query('DROP INDEX IF EXISTS idx_orders_status_email_status');
        $this->db->query('DROP INDEX IF EXISTS idx_orders_created_at_status');
    }
}
