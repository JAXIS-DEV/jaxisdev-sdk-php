<?php

namespace Jaxis;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\Create;
use Jaxis\Auth\AuthModule;
use Jaxis\Sms\SmsModule;
use Jaxis\Whatsapp\WhatsappModule;
use Psr\Http\Message\RequestInterface;

class Jaxis
{
    private array $config;
    private Client $httpClient;

    public AuthModule $auth;
    public SmsModule $sms;
    public WhatsappModule $whatsapp;

    /**
     * @param array{
     *   baseUrl?: string,
     *   auth?: array{username?: string, password?: string},
     *   serviceToken?: string
     * } $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;

        $stack = HandlerStack::create();
        $stack->push($this->authMiddleware(), 'jaxis_auth');

        $this->httpClient = new Client([
            // Sin /v2 en el base_uri a proposito: cada modulo lo declara en su ruta
            // (/v2/messages/sms, /v2/auth/login, ...) para que la version quede
            // explicita en el codigo y no escondida en la config.
            'base_uri' => $config['baseUrl'] ?? 'https://api.jaxis.mx',
            'headers' => ['Content-Type' => 'application/json'],
            'handler' => $stack,
        ]);

        $this->auth = new AuthModule($this->httpClient, $config);
        $this->sms = new SmsModule($this->httpClient);
        $this->whatsapp = new WhatsappModule($this->httpClient);
    }

    /**
     * Inyecta el Bearer token en cada request y, si el servidor responde 401,
     * refresca el token y reintenta una vez -- equivalente a los interceptores
     * request/response del SDK de Node.
     */
    private function authMiddleware(): callable
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                $request = $this->withAuthHeader($request);

                return $handler($request, $options)->then(
                    null,
                    function ($reason) use ($handler, $request, $options) {
                        $response = $reason instanceof RequestException ? $reason->getResponse() : null;

                        if (!$response || $response->getStatusCode() !== 401 || !empty($options['_jaxis_retried'])) {
                            return Create::rejectionFor($reason);
                        }

                        $this->auth->refreshToken();

                        $retryRequest = $this->withAuthHeader($request);
                        $options['_jaxis_retried'] = true;

                        return $handler($retryRequest, $options);
                    }
                );
            };
        };
    }

    private function withAuthHeader(RequestInterface $request): RequestInterface
    {
        // Prioriza el token de servicio -- es el modo de auth pensado para
        // el uso real de este SDK (integraciones servidor-a-servidor).
        if (!empty($this->config['serviceToken'])) {
            return $request->withHeader('Authorization', 'Bearer ' . $this->config['serviceToken']);
        }

        // Fallback: login humano via Cognito (paneles/scripts admin).
        $token = $this->auth->getValidToken();

        return $token !== null ? $request->withHeader('Authorization', 'Bearer ' . $token) : $request;
    }
}
