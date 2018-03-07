<?php

namespace SimpleSkypeBot\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use SimpleSkypeBot\DTO\CoversationDTO;
use SimpleSkypeBot\DTO\MessageDTO;
use SimpleSkypeBot\Exceptions\SimpleSkypeBotException;
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
    protected $directlineEndpoint;

    /**
     * @var string
     */
    protected $connectorEndpoint;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     * @param string $loginEndpoint
     * @param string $connectorEndpoint
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(
        LoggerInterface $logger,
        string $loginEndpoint,
        string $connectorEndpoint,
        string $clientId,
        string $clientSecret
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->loginEndpoint = $loginEndpoint;
        $this->connectorEndpoint = $connectorEndpoint;

        $this->logger = $logger;
    }

    /**
     * @param SkypeToken $token
     * @param MessageDTO $messageDTO
     * @return array
     */
    public function sendConnectorMessage(
        SkypeToken $token,
        MessageDTO $messageDTO
    ): array {
        $data = [
            'type' => 'message',
            'channelId' => 'skype',
            'from' => [
                'id' => $messageDTO->getFromId()
            ],
            'text' => $messageDTO->getText(),
        ];

        if (!$messageDTO->getServiceUrl()) {
            $serviceUrl = $this->connectorEndpoint;
        } else {
            $serviceUrl = $messageDTO->getServiceUrl();
        }
        $this->logger->debug(print_r($data, true), [self::class]);
        $this->logger->debug(print_r($serviceUrl, true), [self::class]);

        $client = new Client(['base_uri' => $serviceUrl]);
        $response = $client->request(
            'POST',
            '/v3/conversations/' . $messageDTO->getConversationId() . '/activities/',
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

        $this->logger->debug($response->getBody(), [self::class, $response->getStatusCode()]);

        if ($response->getStatusCode() === Response::HTTP_CREATED) {
            return \GuzzleHttp\json_decode($response->getBody(), true);
        }

        return [];
    }

    /**
     * @return array
     */
    public function createAuth2Token(): array
    {
        $client = new Client(['base_uri' => $this->loginEndpoint]);
        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
            'scope' => 'https://api.botframework.com/.default',
        ];

        $this->logger->debug(print_r($data, true), [self::class]);

        $response = $client->request(
            'POST',
            '/common/oauth2/v2.0/token',
            [
                RequestOptions::FORM_PARAMS => $data,
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Cache-Control' => 'no-cache',
                ],
            ]
        );

        $this->logger->debug($response->getBody(), [self::class, $response->getStatusCode()]);

        if ($response->getStatusCode() === Response::HTTP_OK) {
            return \GuzzleHttp\json_decode($response->getBody(), true);
        }

        return [];
    }
}

