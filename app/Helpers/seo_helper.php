<?php

if (! function_exists('seo_store_name')) {
    /**
     * Nama toko yang benar-benar diisi pemilik, bukan placeholder.
     */
    function seo_store_name(): ?string
    {
        $name = trim((string) config('Store')->name);

        return $name !== '' ? $name : null;
    }
}

if (! function_exists('seo_title')) {
    function seo_title(string $title): string
    {
        $title = trim($title);
        $store = seo_store_name();

        if ($title === '' || $store === null || mb_strtolower($title) === mb_strtolower($store)) {
            return $title;
        }

        return $title . ' · ' . $store;
    }
}

if (! function_exists('seo_description')) {
    function seo_description(?string $text, int $limit = 155): string
    {
        $text = strip_tags((string) $text);
        $text = trim((string) preg_replace('/\s+/u', ' ', $text));

        if ($text === '' || mb_strlen($text) <= $limit) {
            return $text;
        }

        $cut   = mb_substr($text, 0, $limit - 1);
        $space = mb_strrpos($cut, ' ');

        if ($space !== false && $space > (int) ($limit * 0.6)) {
            $cut = mb_substr($cut, 0, $space);
        }

        return rtrim($cut, " \t\n\r\0\x0B,.;:-") . '…';
    }
}

if (! function_exists('seo_first_sentence')) {
    function seo_first_sentence(?string $text): string
    {
        $text = trim((string) preg_replace('/\s+/u', ' ', strip_tags((string) $text)));

        if ($text === '') {
            return '';
        }

        if (preg_match('/^(.+?[.!?])(?:\s|$)/u', $text, $matches) === 1) {
            return $matches[1];
        }

        return $text;
    }
}

if (! function_exists('seo_canonical')) {
    /**
     * URL kanonis tanpa query string, memakai baseURL yang dikonfigurasi.
     */
    function seo_canonical(): string
    {
        $path = ltrim(uri_string(), '/');

        if ($path === '') {
            return base_url('/');
        }

        return base_url($path);
    }
}

if (! function_exists('seo_site_schemas')) {
    /**
     * Organization dan WebSite hanya dipasang jika nama toko sudah diisi.
     *
     * @return list<array<string, mixed>>
     */
    function seo_site_schemas(): array
    {
        $name = seo_store_name();

        if ($name === null) {
            return [];
        }

        $base  = rtrim(base_url('/'), '/');
        $orgId = $base . '/#organization';
        $tagline = trim((string) config('Store')->tagline);

        $organization = [
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            '@id'      => $orgId,
            'name'     => $name,
            'url'      => $base . '/',
        ];

        if ($tagline !== '') {
            $organization['description'] = $tagline;
        }

        return [
            $organization,
            [
                '@context'   => 'https://schema.org',
                '@type'      => 'WebSite',
                '@id'        => $base . '/#website',
                'url'        => $base . '/',
                'name'       => $name,
                'inLanguage' => 'id-ID',
                'publisher'  => ['@id' => $orgId],
            ],
        ];
    }
}

if (! function_exists('seo_breadcrumb_schema')) {
    /**
     * @param list<array{name: string, url: string}> $items
     *
     * @return array<string, mixed>
     */
    function seo_breadcrumb_schema(array $items): array
    {
        $list     = [];
        $position = 1;

        foreach ($items as $item) {
            if (($item['name'] ?? '') === '' || ($item['url'] ?? '') === '') {
                continue;
            }

            $list[] = [
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $item['name'],
                'item'     => $item['url'],
            ];
        }

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }
}

if (! function_exists('seo_product_image')) {
    /**
     * @param array<string, mixed> $product
     */
    function seo_product_image(array $product): string
    {
        $image = trim((string) ($product['gambar'] ?? ''));

        if ($image === '') {
            return '';
        }

        return preg_match('#^https?://#i', $image) === 1 ? $image : base_url($image);
    }
}

if (! function_exists('seo_product_schema')) {
    /**
     * @param array<string, mixed> $product
     *
     * @return array<string, mixed>
     */
    function seo_product_schema(array $product): array
    {
        $url   = site_url('produk/' . $product['slug']);
        $price = product_price($product);

        $offer = [
            '@type'         => 'Offer',
            'url'           => $url,
            'priceCurrency' => 'IDR',
            'price'         => $price,
            'availability'  => 'https://schema.org/InStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ];

        if (product_promo_state($product) === 'aktif' && ! empty($product['promo_berakhir'])) {
            $until = strtotime((string) $product['promo_berakhir']);

            if ($until !== false) {
                $offer['priceValidUntil'] = date('Y-m-d', $until);
            }
        }

        $store = seo_store_name();

        if ($store !== null) {
            $offer['seller'] = ['@id' => rtrim(base_url('/'), '/') . '/#organization'];
        }

        $schema = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            'name'        => $product['nama'],
            'description' => seo_description($product['deskripsi'] ?? '', 300),
            'url'         => $url,
            'offers'      => $offer,
        ];

        if ($store !== null) {
            $schema['brand'] = ['@type' => 'Brand', 'name' => $store];
        }

        $image = seo_product_image($product);

        if ($image !== '') {
            $schema['image'] = $image;
        }

        return $schema;
    }
}

if (! function_exists('seo_faq_schema')) {
    /**
     * @param list<array{q: string, a: string}> $faqs
     *
     * @return array<string, mixed>
     */
    function seo_faq_schema(array $faqs): array
    {
        $questions = [];

        foreach ($faqs as $faq) {
            if (($faq['q'] ?? '') === '' || ($faq['a'] ?? '') === '') {
                continue;
            }

            $questions[] = [
                '@type'          => 'Question',
                'name'           => $faq['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $faq['a'],
                ],
            ];
        }

        return [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $questions,
        ];
    }
}

if (! function_exists('seo_json_ld')) {
    /**
     * JSON-LD siap cetak. Tag HTML di-escape agar tidak bisa menutup script.
     *
     * @param list<array<string, mixed>> $schemas
     */
    function seo_json_ld(array $schemas): string
    {
        if ($schemas === []) {
            return '';
        }

        $json = json_encode(
            $schemas,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG
        );

        return $json === false ? '' : $json;
    }
}
