<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('admin_id') !== null) {
            return redirect()->to('admin');
        }

        return view('admin/auth/login', [
            'store' => config('Store'),
        ]);
    }

    public function attempt()
    {
        $email    = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');
        $store    = config('Store');

        $lockedUntil = (int) session()->get('login_locked_until');

        if ($lockedUntil > time()) {
            return redirect()->back()->with('error', 'Terlalu banyak percobaan masuk. Coba lagi dalam beberapa menit.');
        }

        if ($store->adminPasswordHash === '' || $email !== strtolower($store->adminEmail) || ! password_verify($password, $store->adminPasswordHash)) {
            $attempts = ((int) session()->get('login_attempts')) + 1;

            session()->set('login_attempts', $attempts);

            if ($attempts >= 5) {
                session()->set('login_locked_until', time() + 300);
                session()->set('login_attempts', 0);
            }

            return redirect()->back()->with('error', 'Email atau kata sandi tidak cocok.');
        }

        session()->regenerate(true);
        session()->set([
            'admin_id'    => 1,
            'admin_name'  => $store->adminName,
            'admin_email' => $store->adminEmail,
        ]);
        session()->remove(['login_attempts', 'login_locked_until']);

        return redirect()->to('admin')->with('success', 'Selamat datang, ' . $store->adminName . '.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('admin/login')->with('success', 'Anda sudah keluar dari dashboard.');
    }
}
