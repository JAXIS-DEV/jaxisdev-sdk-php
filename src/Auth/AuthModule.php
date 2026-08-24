<?php

namespace Jaxis\Auth;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;
use Jaxis\Exceptions\AuthenticationException;

class AuthModule
{
    private ClientInterface $client;
    private array $config;
    private ?string $currentToken = null;

    public function __construct(ClientInterface $client, array $config)
    {
        $this->client = $client;
        $this->config = $config;
    }

    public function getValidToken(): ?string
    {
        if (!empty($this->config['serviceToken'])) {
            return null;
        }
        if ($this->currentToken !== null) {
            return $this->currentToken;
        }

        if (!empty($this->config['auth']['username']) && !empty($this->config['auth']['password'])) {
            $this->signIn();
        }

        return $this->currentToken;
    }

    public function refreshToken(): void
    {
        $this->currentToken = null;
        $this->signIn();
    }

    public function signIn(?string $username = null, ?string $password = null): array
    {
        $loginUsername = $username ?? ($this->config['auth']['username'] ?? null);
        $loginPassword = $password ?? ($this->config['auth']['password'] ?? null);

        if (!$loginUsername || !$loginPassword) {
            throw new InvalidArgumentException('Missing credentials for JWT negotiation');
        }

        try {
            $response = $this->client->request('POST', '/v2/auth/login', [
                'json' => [
                    'username' => $loginUsername,
                    'password' => $loginPassword,
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true) ?? [];

            // Extrae el IdToken tal como lo requieren nativamente los endpoints de Jaxis.
            $this->currentToken = $data['AuthenticationResult']['IdToken'] ?? null;

            return $data;
        } catch (RequestException $e) {
            $body = $e->getResponse() ? (string) $e->getResponse()->getBody() : '';
            $decoded = json_decode($body, true);
            $apiMessage = $decoded['message'] ?? '';

            if (
                str_contains($apiMessage, 'NotAuthorizedException')
                || str_contains($apiMessage, 'Incorrect username or password')
            ) {
                throw new AuthenticationException(
                    'Jaxis Authentication Failed: Incorrect username or password. Please verify your configured credentials.'
                );
            }

            throw $e;
        }
    }
}
