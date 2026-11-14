<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommerceTables extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'                    => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama'                  => ['type' => 'VARCHAR', 'constraint' => 150],
            'slug'                  => ['type' => 'VARCHAR', 'constraint' => 160],
            'harga'                 => ['type' => 'INTEGER', 'constraint' => 12, 'default' => 0],
            'tingkat_kompleksitas'  => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'Sederhana'],
            'deskripsi'             => ['type' => 'TEXT'],
            'fitur'                 => ['type' => 'TEXT', 'null' => true],
            'gambar'                => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'link_demo'             => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'link_template'         => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'link_video'            => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'link_panduan'          => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'aktif'                 => ['type' => 'INTEGER', 'constraint' => 1, 'default' => 1],
            'urutan'                => ['type' => 'INTEGER', 'constraint' => 5, 'default' => 0],
            'created_at'            => ['type' => 'DATETIME', 'null' => true],
            'updated_at'            => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('products', true);

        $this->forge->addField([
            'id'                => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'          => ['type' => 'VARCHAR', 'constraint' => 40],
            'nomor_invoice'     => ['type' => 'VARCHAR', 'constraint' => 40],
            'access_token'      => ['type' => 'VARCHAR', 'constraint' => 64],
            'product_id'        => ['type' => 'INTEGER', 'constraint' => 11],
            'product_nama'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'product_harga'     => ['type' => 'INTEGER', 'constraint' => 12],
            'nama_pembeli'      => ['type' => 'VARCHAR', 'constraint' => 120],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 190],
            'whatsapp'          => ['type' => 'VARCHAR', 'constraint' => 30],
            'nominal'           => ['type' => 'INTEGER', 'constraint' => 12],
            'status'            => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'pending'],
            'payment_method'    => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'payment_reference' => ['type' => 'VARCHAR', 'constraint' => 190, 'null' => true],
            'payment_url'       => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            'paid_at'           => ['type' => 'DATETIME', 'null' => true],
            'expires_at'        => ['type' => 'DATETIME', 'null' => true],
            'status_email'      => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'belum'],
            'email_sent_at'     => ['type' => 'DATETIME', 'null' => true],
            'jumlah_retry'      => ['type' => 'INTEGER', 'constraint' => 5, 'default' => 0],
            'catatan'           => ['type' => 'TEXT', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_id');
        $this->forge->addUniqueKey('nomor_invoice');
        $this->forge->addKey('email');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        $this->forge->createTable('orders', true);

        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'   => ['type' => 'VARCHAR', 'constraint' => 40],
            'tipe'       => ['type' => 'VARCHAR', 'constraint' => 40],
            'pesan'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'payload'    => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->createTable('order_events', true);

        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'   => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'jenis'      => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'akses'],
            'penerima'   => ['type' => 'VARCHAR', 'constraint' => 190],
            'subjek'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'terkirim'],
            'error'      => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('order_id');
        $this->forge->createTable('email_logs', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('email_logs', true);
        $this->forge->dropTable('order_events', true);
        $this->forge->dropTable('orders', true);
        $this->forge->dropTable('products', true);
    }
}
