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

        return view('layouts/public', [
            'title'       => $meta['title'] ?? store_name(),
            'description' => $meta['description'] ?? $store->tagline,
            'active'      => $meta['active'] ?? '',
            'content'     => view($view, $data),
            'store'       => $store,
        ]);
    }

    protected function renderAdmin(string $view, array $data = [], array $meta = []): string
    {
        $store = config('Store');
        $data['store'] = $store;

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
}
