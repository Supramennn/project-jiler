<?php

use App\Filters\ThrottleRequests;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ThrottleRequestsTest extends CIUnitTestCase
{
    public function testSecondRequestIsThrottledWithHeaders(): void
    {
        $_SERVER['REMOTE_ADDR'] = '203.0.113.9';

        $request = service('request');
        $filter  = new ThrottleRequests();
        $bucket  = 'test-' . bin2hex(random_bytes(4));

        $this->assertNull($filter->before($request, ['1', '60', $bucket]));

        $response = $filter->before($request, ['1', '60', $bucket]);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertSame(429, $response->getStatusCode());
        $this->assertNotSame('', $response->getHeaderLine('Retry-After'));
        $this->assertSame('SAMEORIGIN', $response->getHeaderLine('X-Frame-Options'));
        $this->assertSame('nosniff', $response->getHeaderLine('X-Content-Type-Options'));
        $this->assertStringContainsString('Terlalu banyak percobaan', $response->getBody());
    }
}
