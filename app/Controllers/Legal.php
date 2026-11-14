<?php

namespace App\Controllers;

class Legal extends BaseController
{
    public function terms(): string
    {
        return $this->renderPublic('legal/terms', [], [
            'title'  => 'Syarat dan ketentuan',
            'active' => '',
        ]);
    }

    public function privacy(): string
    {
        return $this->renderPublic('legal/privacy', [], [
            'title'  => 'Kebijakan privasi',
            'active' => '',
        ]);
    }
}
