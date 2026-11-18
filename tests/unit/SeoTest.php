<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class SeoTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper(['app', 'seo', 'url']);
    }

    public function testDescriptionStripsMarkupAndCollapsesWhitespace(): void
    {
        $description = seo_description("<p>Halo <strong>dunia</strong>.</p>\n\nIni   uji.");

        $this->assertSame('Halo dunia. Ini uji.', $description);
    }

    public function testDescriptionStopsAtWordBoundaryAndAddsEllipsis(): void
    {
        $description = seo_description(str_repeat('kata ', 60), 30);

        $this->assertLessThanOrEqual(30, mb_strlen($description));
        $this->assertStringEndsWith('…', $description);
        $this->assertStringNotContainsString('  ', $description);
    }

    public function testFirstSentenceReturnsCompleteSentence(): void
    {
        $this->assertSame(
            'Template untuk mencatat stok.',
            seo_first_sentence('Template untuk mencatat stok. Panduan dikirim lewat email.')
        );
    }

    public function testBreadcrumbSchemaKeepsOrderAndSkipsEmptyItems(): void
    {
        $schema = seo_breadcrumb_schema([
            ['name' => 'Beranda', 'url' => 'https://example.test/'],
            ['name' => '', 'url' => 'https://example.test/kosong'],
            ['name' => 'Produk', 'url' => 'https://example.test/produk'],
        ]);

        $this->assertSame('BreadcrumbList', $schema['@type']);
        $this->assertCount(2, $schema['itemListElement']);
        $this->assertSame(1, $schema['itemListElement'][0]['position']);
        $this->assertSame('Beranda', $schema['itemListElement'][0]['name']);
        $this->assertSame(2, $schema['itemListElement'][1]['position']);
        $this->assertSame('Produk', $schema['itemListElement'][1]['name']);
    }

    public function testProductSchemaUsesPromoPriceAndValidityDate(): void
    {
        $until   = date('Y-m-d H:i:s', time() + 86400);
        $product = [
            'nama'           => 'Produk Uji',
            'slug'           => 'produk-uji',
            'harga'          => 100000,
            'harga_promo'    => 80000,
            'promo_mulai'    => null,
            'promo_berakhir' => $until,
            'deskripsi'      => '<p>Deskripsi produk uji.</p>',
            'gambar'         => '',
        ];

        $schema = seo_product_schema($product);

        $this->assertSame('Product', $schema['@type']);
        $this->assertSame(80000, $schema['offers']['price']);
        $this->assertSame('IDR', $schema['offers']['priceCurrency']);
        $this->assertSame('https://schema.org/InStock', $schema['offers']['availability']);
        $this->assertSame(date('Y-m-d', strtotime($until)), $schema['offers']['priceValidUntil']);
        $this->assertStringContainsString('/produk/produk-uji', $schema['url']);
    }

    public function testFaqSchemaBuildsQuestionList(): void
    {
        $schema = seo_faq_schema([
            ['q' => 'Pertanyaan satu?', 'a' => 'Jawaban satu.'],
            ['q' => 'Pertanyaan dua?', 'a' => 'Jawaban dua.'],
        ]);

        $this->assertSame('FAQPage', $schema['@type']);
        $this->assertCount(2, $schema['mainEntity']);
        $this->assertSame('Question', $schema['mainEntity'][0]['@type']);
        $this->assertSame('Answer', $schema['mainEntity'][0]['acceptedAnswer']['@type']);
    }

    public function testJsonLdEscapesClosingScriptTag(): void
    {
        $json = seo_json_ld([
            ['@type' => 'Thing', 'name' => '</script><script>alert(1)</script>'],
        ]);

        $this->assertStringNotContainsString('</script>', $json);
        $this->assertStringContainsString('\u003C/script\u003E', $json);
    }
}
