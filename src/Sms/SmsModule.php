<?php

namespace Jaxis\Sms;

use GuzzleHttp\ClientInterface;
use InvalidArgumentException;

class SmsModule
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param array{to: string|string[], text: string} $params
     */
    public function send(array $params): array
    {
        $to = $params['to'] ?? null;
        $destinations = is_array($to) ? $to : ($to !== null ? [$to] : []);

        if (count($destinations) === 0) {
            throw new InvalidArgumentException("El parámetro 'to' debe contener al menos un número telefónico.");
        }

        // /v2/svc/... -- protegida con el Lambda authorizer de tokens de
        // servicio, igual que whatsapp.sendBatch (ver src/Whatsapp).
        $response = $this->client->request('POST', '/v2/svc/messages/sms', [
            'json' => [
                'destinations' => $destinations,
                'text' => $params['text'] ?? null,
            ],
        ]);

        return json_decode((string) $response->getBody(), true) ?? [];
    }
}
