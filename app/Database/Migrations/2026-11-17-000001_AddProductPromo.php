<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductPromo extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('products', [
            'harga_promo'    => ['type' => 'INTEGER', 'constraint' => 12, 'null' => true],
            'promo_mulai'    => ['type' => 'DATETIME', 'null' => true],
            'promo_berakhir' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', ['harga_promo', 'promo_mulai', 'promo_berakhir']);
    }
}
