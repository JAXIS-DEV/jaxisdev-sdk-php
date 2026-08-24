<?php

namespace Jaxis\Whatsapp;

use GuzzleHttp\ClientInterface;

class WhatsappModule
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * Un solo destinatario -- por debajo usa el mismo endpoint batch con un solo elemento.
     *
     * @param array{
     *   to: string,
     *   templateId: string,
     *   params?: array<string, string|int>,
     *   buttonValue?: string,
     *   headerMedia?: array{type: string, url: string, filename?: string},
     *   imageUrl?: string
     * } $params
     */
    public function send(array $params): array
    {
        $to = $params['to'];
        unset($params['to']);

        $batchResponse = $this->sendBatch(['destinations' => [$to]] + $params);
        $detail = $batchResponse['details'][0] ?? [];

        return [
            'id' => $detail['id'] ?? null,
            'status' => $detail['status'] ?? 'failed',
            'errorCode' => $detail['errorCode'] ?? null,
            'errorMsg' => $detail['errorMsg'] ?? null,
        ];
    }

    /**
     * @param array{
     *   destinations: string[],
     *   templateId: string,
     *   params?: array<string, string|int>,
     *   buttonValue?: string,
     *   headerMedia?: array{type: string, url: string, filename?: string},
     *   imageUrl?: string
     * } $params
     */
    public function sendBatch(array $params): array
    {
        // /v2/svc/... -- rutas protegidas con el Lambda authorizer de tokens de
        // servicio (no el JWT nativo de Cognito), que es el modo de auth real
        // para el que esta pensado este SDK.
        $response = $this->client->request('POST', '/v2/svc/messages/whatsapp/batch', [
            'json' => $params,
        ]);

        return json_decode((string) $response->getBody(), true) ?? [];
    }
}
