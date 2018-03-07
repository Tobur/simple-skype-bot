<?php

namespace SimpleSkypeBot\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use SimpleSkypeBot\Exceptions\SImpleSkypeBotException;
use SimpleSkypeBot\Model\SkypeToken;
use SimpleSkypeBot\Model\SkypeUser;
use Symfony\Component\HttpFoundation\Response;

class SkypeApiClient
{
    /**
     * @var string
     */
    protected $loginEndpoint;

    /**
     * @var string
     */
    protected $botEndpoint;

    /**
     * @var string
     */
    protected $smbaEndpoint;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $botSecretKey;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     * @param string $loginEndpoint
     * @param string $botEndpoint
     * @param string $smbaEndpoint
     * @param string $clientId
     * @param string $clientSecret
     * @param string $botSecretKey
     */
    public function __construct(
        LoggerInterface $logger,
        string $loginEndpoint,
        string $botEndpoint,
        string $smbaEndpoint,
        string $clientId,
        string $clientSecret,
        string $botSecretKey
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->botEndpoint = $botEndpoint;
        $this->loginEndpoint = $loginEndpoint;
        $this->botSecretKey = $botSecretKey;
        $this->smbaEndpoint = $smbaEndpoint;

        $this->logger = $logger;
    }

    /**
     * @return array
     */
    public function createAuth2Token(): array
    {
         $client = new Client(['base_uri' => $this->loginEndpoint]);
         $response = $client->request(
            'POST',
            '/common/oauth2/v2.0/token',
            [
                RequestOptions::FORM_PARAMS => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.botframework.com/.default'
                ],
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Cache-Control' => 'no-cache'
                ]
            ]
        );

        $this->logger->debug($response->getBody(), [static::class, $response->getStatusCode()]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return \GuzzleHttp\json_decode($response->getBody(), true);
        }

        return [];
    }

    /**
     * @param SkypeToken $token
     * @throws SimpleSkypeBotException
     */
    public function createConversation(SkypeToken $token): array
    {
        $client = new Client(['base_uri' => $this->botEndpoint]);
        $response = $client->request(
            'POST',
            '/v3/directline/conversations',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => sprintf(
                        'Bearer %s',
                        $this->botSecretKey
                    )
                ]
            ]
        );

        $this->logger->debug($response->getBody(), [static::class, $response->getStatusCode()]);

        if ($response->getStatusCode() === Response::HTTP_CREATED) {
            return \GuzzleHttp\json_decode($response->getBody(), true);
        }

        return [];
    }

    /**
     * @param SkypeToken $token
     * @param SkypeUser $skypeUser
     * @param null|string $serviceUrl
     * @param string $message
     * @throws
     * @return array
     */
    public function sendMessage(
        SkypeToken $token,
        SkypeUser $skypeUser,
        ?string $serviceUrl,
        string $message
    ): array {
        if ($token->getTokenType() === SkypeToken::API_TYPE_DIRECT_LINE) {
            throw new SimpleSkypeBotException(
                sprintf(
                    'Create conversation support only %s token type.',
                    SkypeToken::API_TYPE_AUTH2
                )
            );
        }

        $data = [
            'type' => 'message',
            'channelId' => 'skype',
            'recipient' => [
                'id' => $skypeUser->getSkypeLoginId() ? $skypeUser->getSkypeLoginId() : $skypeUser->getSkypeLogin()
            ],
            'text' => $message,
        ];

        if (!$serviceUrl) {
            $serviceUrl = $this->smbaEndpoint;
        }

        $client = new Client(['base_uri' => $serviceUrl]);
        $response = $client->request(
            'POST',
            '/v3/conversations/' . $skypeUser->getConversationId() . '/activities/',
            [
                RequestOptions::JSON => $data,
                RequestOptions::HEADERS => [
                    'Accept' => 'application/json',
                    'Authorization' => sprintf(
                        '%s %s',
                        $token->getTokenType(),
                        $token->getAccessToken()
                    )
                ]
            ]
        );

        $this->logger->debug($response->getBody(), [static::class, $response->getStatusCode()]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return \GuzzleHttp\json_decode($response->getBody(), true);
        }

        return [];
    }

    /**
     * @return array
     */
    public function generateDirectlineToken(): array
    {
        $client = new Client(['base_uri' => $this->botEndpoint]);
        $response = $client->request(
            'POST',
            '/v3/directline/tokens/generate',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => sprintf(
                        'Bearer %s',
                        $this->botSecretKey
                    )
                ]
            ]
        );

        $this->logger->debug($response->getBody(), [static::class, $response->getStatusCode()]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return \GuzzleHttp\json_decode($response->getBody(), true);
        }

        return [];
    }
}

