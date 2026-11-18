<?php

use App\Libraries\OrderCsv;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class OrderCsvTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('app');
    }

    public function testBuildAddsBomHeaderAndOrderRow(): void
    {
        $csv = (new OrderCsv())->build([$this->order()]);

        $this->assertStringStartsWith("\xEF\xBB\xBF", $csv);
        $this->assertStringContainsString('Nomor invoice', $csv);
        $this->assertStringContainsString('INV/202611/0001', $csv);
        $this->assertStringContainsString('22000', $csv);
        $this->assertStringContainsString('88000', $csv);
        $this->assertStringContainsString('Sudah dibayar', $csv);
    }

    public function testBuildNeutralizesSpreadsheetFormulas(): void
    {
        $order           = $this->order();
        $order['nama_pembeli'] = '=SUM(1,2)';
        $order['whatsapp']     = '+6281234567890';

        $csv = (new OrderCsv())->build([$order]);

        $this->assertStringContainsString("'=SUM(1,2)", $csv);
        $this->assertStringContainsString("'+6281234567890", $csv);
    }

    public function testDiscountNeverNegative(): void
    {
        $order              = $this->order();
        $order['nominal']   = 120000;
        $order['product_harga'] = 110000;

        $csv = (new OrderCsv())->build([$order]);

        $this->assertStringContainsString(',0,120000,', $csv);
    }

    /**
     * @return array<string, mixed>
     */
    private function order(): array
    {
        return [
            'nomor_invoice'     => 'INV/202611/0001',
            'order_id'          => 'INV-261118-ABC123',
            'created_at'        => '2026-11-18 10:00:00',
            'nama_pembeli'      => 'Budi',
            'email'             => 'budi@example.test',
            'whatsapp'          => '081234567890',
            'product_nama'      => 'Manajemen Stok Barang',
            'product_harga'     => 110000,
            'nominal'           => 88000,
            'status'            => 'paid',
            'status_email'      => 'terkirim',
            'payment_method'    => 'permata_va',
            'payment_reference' => '812345678901000',
            'paid_at'           => '2026-11-18 10:05:00',
            'expires_at'        => '2026-11-19 10:00:00',
        ];
    }
}
