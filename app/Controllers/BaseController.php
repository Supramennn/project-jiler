<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['form', 'url', 'app'];

    protected $session;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        $this->session = service('session');
    }

    protected function renderPublic(string $view, array $data = [], array $meta = []): string
    {
        $store = config('Store');
        $data['store'] = $store;

        $robots = (string) ($meta['robots'] ?? 'index,follow');
        $jsonLd = $meta['jsonLd'] ?? [];

        if (! str_contains($robots, 'noindex')) {
            $jsonLd = array_merge(seo_site_schemas(), $jsonLd);
        } else {
            $this->response->setHeader('X-Robots-Tag', 'noindex, nofollow');
        }

        return view('layouts/public', [
            'title'        => $meta['title'] ?? store_name(),
            'description'  => seo_description($meta['description'] ?? $store->tagline, 160),
            'active'       => $meta['active'] ?? '',
            'canonical'    => $meta['canonical'] ?? seo_canonical(),
            'robots'       => $robots,
            'ogType'       => $meta['type'] ?? 'website',
            'ogImage'      => $meta['image'] ?? '',
            'product'      => $meta['product'] ?? null,
            'jsonLd'       => $jsonLd,
            'verification' => (string) env('GOOGLE_SITE_VERIFICATION', ''),
            'content'      => view($view, $data),
            'store'        => $store,
        ]);
    }

    protected function renderAdmin(string $view, array $data = [], array $meta = []): string
    {
        $store = config('Store');
        $data['store'] = $store;
        $this->response->setHeader('Cache-Control', 'no-store, private');

        return view('layouts/admin', [
            'title'   => $meta['title'] ?? 'Dashboard',
            'active'  => $meta['active'] ?? 'dashboard',
            'content' => view($view, $data),
            'store'   => $store,
            'admin'   => [
                'name'  => (string) session()->get('admin_name'),
                'email' => (string) session()->get('admin_email'),
            ],
        ]);
    }

    protected function renderAdminAuth(string $view, array $data = []): string
    {
        $store = config('Store');
        $data['store'] = $store;
        $this->response->setHeader('Cache-Control', 'no-store, private');

        return view('layouts/admin_auth', [
            'title'   => 'Masuk dashboard',
            'content' => view($view, $data),
            'store'   => $store,
        ]);
    }
}
