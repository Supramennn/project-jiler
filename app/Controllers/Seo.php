<?php

namespace App\Controllers;

use App\Models\ProductModel;
use CodeIgniter\HTTP\ResponseInterface;

class Seo extends BaseController
{
    public function sitemap(): ResponseInterface
    {
        $products = model(ProductModel::class)->activeProducts();
        $latest   = null;

        foreach ($products as $product) {
            $updated = (string) ($product['updated_at'] ?? '');

            if ($updated !== '' && ($latest === null || strtotime($updated) > strtotime($latest))) {
                $latest = $updated;
            }
        }

        $lastmod = $this->lastmod($latest);
        $urls    = [
            [
                'loc'        => site_url('/'),
                'lastmod'    => $lastmod,
                'changefreq' => 'weekly',
                'priority'   => '1.0',
            ],
            [
                'loc'        => site_url('produk'),
                'lastmod'    => $lastmod,
                'changefreq' => 'weekly',
                'priority'   => '0.9',
            ],
        ];

        foreach ($products as $product) {
            $urls[] = [
                'loc'        => site_url('produk/' . $product['slug']),
                'lastmod'    => $this->lastmod((string) ($product['updated_at'] ?? '')),
                'changefreq' => 'weekly',
                'priority'   => '0.8',
            ];
        }

        $urls[] = [
            'loc'        => site_url('syarat'),
            'lastmod'    => null,
            'changefreq' => 'yearly',
            'priority'   => '0.3',
        ];
        $urls[] = [
            'loc'        => site_url('privasi'),
            'lastmod'    => null,
            'changefreq' => 'yearly',
            'priority'   => '0.3',
        ];

        return $this->response
            ->setContentType('application/xml')
            ->setBody($this->sitemapXml($urls));
    }

    public function robots(): ResponseInterface
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /checkout',
            'Disallow: /pembayaran',
            'Disallow: /sukses',
            'Disallow: /gagal',
            'Disallow: /invoice',
            'Disallow: /status',
            'Disallow: /webhook',
            'Disallow: /cron',
            '',
            'Sitemap: ' . site_url('sitemap.xml'),
        ];

        return $this->response
            ->setContentType('text/plain')
            ->setBody(implode("\n", $lines) . "\n");
    }

    /**
     * @param list<array<string, string|null>> $urls
     */
    private function sitemapXml(array $urls): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "    <url>\n";
            $xml .= '        <loc>' . $this->xml($url['loc'] ?? '') . "</loc>\n";

            foreach (['lastmod', 'changefreq', 'priority'] as $tag) {
                if (! empty($url[$tag])) {
                    $xml .= '        <' . $tag . '>' . $this->xml($url[$tag]) . '</' . $tag . ">\n";
                }
            }

            $xml .= "    </url>\n";
        }

        return $xml . "</urlset>\n";
    }

    private function xml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function lastmod(string $datetime): ?string
    {
        if ($datetime === '') {
            return null;
        }

        $timestamp = strtotime($datetime);

        return $timestamp === false ? null : date('c', $timestamp);
    }
}
