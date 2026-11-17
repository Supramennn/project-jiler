<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ProductPromoTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('app');
    }

    public function testProductWithoutPromoUsesNormalPrice(): void
    {
        $product = $this->product();

        $this->assertSame('none', product_promo_state($product));
        $this->assertFalse(product_promo_active($product));
        $this->assertSame(100000, product_price($product));
        $this->assertSame(0, product_discount_amount($product));
        $this->assertSame(0, product_discount_percent($product));
    }

    public function testActivePromoUsesPromoPrice(): void
    {
        $product = $this->product(['harga_promo' => 79000]);

        $this->assertSame('aktif', product_promo_state($product));
        $this->assertTrue(product_promo_active($product));
        $this->assertSame(79000, product_price($product));
        $this->assertSame(21000, product_discount_amount($product));
        $this->assertSame(21, product_discount_percent($product));
    }

    public function testFuturePromoIsNotActiveYet(): void
    {
        $product = $this->product([
            'harga_promo' => 79000,
            'promo_mulai' => date('Y-m-d H:i:s', time() + 3600),
        ]);

        $this->assertSame('terjadwal', product_promo_state($product));
        $this->assertSame(100000, product_price($product));
    }

    public function testExpiredPromoReturnsToNormalPrice(): void
    {
        $product = $this->product([
            'harga_promo' => 79000,
            'promo_berakhir' => date('Y-m-d H:i:s', time() - 3600),
        ]);

        $this->assertSame('berakhir', product_promo_state($product));
        $this->assertSame(100000, product_price($product));
    }

    public function testExpiredPromoAtExactEndTimeReturnsToNormalPrice(): void
    {
        $ts = time();
        $product = $this->product([
            'harga_promo' => 79000,
            'promo_mulai' => date('Y-m-d H:i:s', $ts - 3600),
            'promo_berakhir' => date('Y-m-d H:i:s', $ts),
        ]);

        $this->assertSame('berakhir', product_promo_state($product, $ts));
        $this->assertSame(100000, product_price($product));
    }

    public function testPromoStartsExactlyOnScheduleReturnsPromoPrice(): void
    {
        $ts = time();
        $product = $this->product([
            'harga_promo' => 79000,
            'promo_mulai' => date('Y-m-d H:i:s', $ts),
            'promo_berakhir' => date('Y-m-d H:i:s', $ts + 3600),
        ]);

        $this->assertSame('aktif', product_promo_state($product, $ts));
        $this->assertSame(79000, product_price($product));
    }

    public function testPromoMustBeLowerThanNormalPrice(): void
    {
        $product = $this->product(['harga_promo' => 100000]);

        $this->assertSame('none', product_promo_state($product));
        $this->assertSame(100000, product_price($product));
    }

    public function testActivePromoInsideScheduleWindow(): void
    {
        $product = $this->product([
            'harga_promo' => 79000,
            'promo_mulai' => date('Y-m-d H:i:s', time() - 3600),
            'promo_berakhir' => date('Y-m-d H:i:s', time() + 3600),
        ]);

        $this->assertSame('aktif', product_promo_state($product));
        $this->assertSame(79000, product_price($product));
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function product(array $overrides = []): array
    {
        return array_merge([
            'harga' => 100000,
            'harga_promo' => null,
            'promo_mulai' => null,
            'promo_berakhir' => null,
        ], $overrides);
    }
}
