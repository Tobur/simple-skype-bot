<?php

namespace SimpleSkypeBot\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SimpleSkypeBot\DTO\CoversationDTO;
use SimpleSkypeBot\DTO\MessageDTO;
use SimpleSkypeBot\Model\SkypeToken;
use SimpleSkypeBot\Model\SkypeUser;

class SkypeBotManager
{
    /**
     * @var SkypeBotClient
     */
    protected $skypeBotClient;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var string
     */
    protected $userClass;

    /**
     * @var string
     */
    protected $tokenClass;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param SkypeBotClient $skypeBotClient
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     * @param string $userClass
     * @param string $tokenClass
     */
    public function __construct(
        SkypeBotClient $skypeBotClient,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        string $userClass,
        string $tokenClass
    ) {
        $this->skypeBotClient = $skypeBotClient;
        $this->em = $em;
        $this->tokenClass = $tokenClass;
        $this->userClass = $userClass;
        $this->logger = $logger;
    }

    public function sendMessage(string $message, string $skypeLogin)
    {
        $this->logger->debug('Start send message', [static::class]);

        $token = $this->skypeBotClient->generateDirectlineToken();
        $coversation = $this->skypeBotClient->createConversation($token);
        $coversationDTO = new CoversationDTO($coversation);

        $this->skypeBotClient->sendMessage(
            $this->getAuth2Token(),
            $this->getSkypeUserByConversationDTO($coversationDTO, $skypeLogin),
            null,
            $message
        );

        $this->logger->debug('End send message', [static::class]);
    }

    /**
     * @param MessageDTO $message
     * @param string $message
     * @throws SimpleSkypeBotException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \SimpleSkypeBot\Exceptions\SImpleSkypeBotException
     */
    public function sendReplyMessage(MessageDTO $messageDTO, string $message)
    {
        $this->logger->debug('Start send reply message', [static::class]);
        $this->skypeBotClient->sendMessage(
            $this->getAuth2Token(),
            $this->getSkypeUserByMessageDTO($messageDTO),
            $messageDTO->getServiceUrl(),
            $message
        );
        $this->logger->debug('End send reply message', [static::class]);
    }

    /**
     * @TODO lets get token from DB and catch 401 error
     * @return SkypeToken
     * @throws SimpleSkypeBotException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getAuth2Token(): SkypeToken
    {
        $data = $this->skypeBotClient->createAuth2Token();
        if (!isset($data['access_token'])) {
            throw new SimpleSkypeBotException('Token did not create!');
        }
        /** @var SkypeToken $skypeToken */
        $skypeToken = $this->em->getRepository($this->tokenClass)->findOneBy(
            [
                'accessToken' => $data['access_token'],
                'apiType' => SkypeToken::API_TYPE_AUTH2
            ]
        );

        if (empty($skypeToken)) {
            $skypeToken = new $this->tokenClass();
            $this->em->persist($skypeToken);
        }

        $date = new \DateTime();

        if (!empty($skypeToken->getExpiresIn()) && $date < $skypeToken->getExpiresIn()) {
            return $skypeToken;
        }

        $skypeToken->setAccessToken($data['access_token']);
        $skypeToken->setApiType(SkypeToken::API_TYPE_AUTH2);
        $skypeToken->setTokenType($data['token_type']);
        $date->modify(sprintf('+%s seconds', $data['expires_in']));
        $skypeToken->setExpiresIn($date);

        $this->em->flush();

        return $token;
    }

    /**
     * @return SkypeToken
     */
    protected function getDirectLineToken()
    {
        $data = $this->skypeBotClient->generateDirectlineToken();
        if (!isset($data['access_token'])) {
            throw new SimpleSkypeBotException('Token did not create!');
        }
        /** @var SkypeToken $skypeToken */
        $skypeToken = $this->em->getRepository($this->tokenClass)->findOneBy(
            ['token' => $data['token'], 'apiType' => SkypeToken::API_TYPE_DIRECT_LINE]
        );

        if (empty($skypeToken)) {
            $skypeToken = new $this->tokenClass();
            $this->em->persist($skypeToken);
        }

        if (!empty($skypeToken->getExpiresIn()) && $date < $skypeToken->getExpiresIn()) {
            return $skypeToken;
        }

        $skypeToken->setAccessToken($data['token']);
        $skypeToken->setApiType(SkypeToken::API_TYPE_DIRECT_LINE);
        $date = new \DateTime();
        $date->modify(sprintf('+%s seconds', $data['expires_in']));
        $skypeToken->setExpiresIn($date);

        $this->em->flush();

        return $token;
    }


    /**
     * @param Message $data
     * @return SkypeUser
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getSkypeUserByMessageDTO(MessageDTO $message): SkypeUser
    {
        $skypeUser = $this->em
            ->getRepository($this->userClass)
            ->findOneBy(['skypeLoginId' => $message->getFromId()]);

        if ($skypeUser) {
            $skypeUser->setConversationId($message->getConversationId());
            $this->em->flush();

            return $skypeUser;
        }

        $skypeUser = new $this->userClass();
        $skypeUser->setSkypeLoginId($message->getFromId());
        $skypeUser->setSkypeName($message->getFromId());
        $skypeUser->setConversationId($message->getConversationId());

        $this->em->persist($skypeUser);
        $this->em->flush();

        return $skypeUser;
    }

    /**
     * @param CoversationDTO $coversationDTO
     * @param string $skypeLogin
     * @return SkypeUser
     */
    protected function getSkypeUserByConversationDTO(
        CoversationDTO $coversationDTO,
        string $skypeLogin
    ): SkypeUser {
        /** @var SkypeUser $skypeUser */
        $skypeUser = $this->em
            ->getRepository($this->userClass)
            ->findOneBy(['conversationId' => $coversationDTO->getConversationId()]);

        if ($skypeUser) {
            $skypeUser->setSkypeLogin($skypeLogin);
            $this->em->flush();

            return $skypeUser;
        }

        $skypeUser = new $this->userClass();
        $skypeUser->setSkypeLogin($skypeLogin);
        $skypeUser->setConversationId($coversationDTO->getConversationId());

        $this->em->persist($skypeUser);
        $this->em->flush();

        return $skypeUser;
    }
}

