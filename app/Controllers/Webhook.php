<?php

namespace App\Controllers;

use App\Libraries\WebhookService;

class Webhook extends BaseController
{
    public function midtrans()
    {
        $raw     = (string) $this->request->getBody();
        $payload = json_decode($raw, true);

        if (! is_array($payload)) {
            return $this->response->setStatusCode(400)->setJSON([
                'ok'      => false,
                'message' => 'Body harus berupa JSON yang valid.',
            ]);
        }

        $signature = (string) ($payload['signature_key'] ?? $this->request->getHeaderLine('X-Sandbox-Signature'));
        $result    = (new WebhookService())->handle($payload, $signature);

        return $this->response->setStatusCode($result['code'])->setJSON([
            'ok'      => $result['ok'],
            'message' => $result['message'],
        ]);
    }
}
