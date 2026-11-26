<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * @internal
 */
final class PaymentAccessTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testOrderIdAloneCannotOpenPaymentPage(): void
    {
        $this->assertOrderIdOnlyRouteIsUnavailable('GET', 'pembayaran/INV-TEST');
    }

    public function testOrderIdAloneCannotOpenSuccessPage(): void
    {
        $this->assertOrderIdOnlyRouteIsUnavailable('GET', 'sukses/INV-TEST');
    }

    public function testOrderIdAloneCannotOpenFailurePage(): void
    {
        $this->assertOrderIdOnlyRouteIsUnavailable('GET', 'gagal/INV-TEST');
    }

    public function testOrderIdAloneCannotChoosePaymentMethod(): void
    {
        $this->assertOrderIdOnlyRouteIsUnavailable('POST', 'pembayaran/INV-TEST/metode');
    }

    public function testOrderIdAloneCannotSimulatePayment(): void
    {
        $this->assertOrderIdOnlyRouteIsUnavailable('POST', 'pembayaran/INV-TEST/simulasi');
    }

    private function assertOrderIdOnlyRouteIsUnavailable(string $method, string $path): void
    {
        $this->expectException(PageNotFoundException::class);
        $this->call($method, $path);
    }
}
