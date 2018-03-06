<?php

namespace SimpleSkypeBot\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
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
        $this->logger->debug('Start send message', [static::class]);
        $this->skypeBotClient->sendMessage(
            $this->getToken(),
            $this->getSkypeUser($messageDTO),
            $messageDTO->getServiceUrl(),
            $message
        );
        $this->logger->debug('End send message', [static::class]);
    }

    /**
     * @TODO lets get token from DB and catch 401 error
     * @return SkypeToken
     * @throws SimpleSkypeBotException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getToken(): SkypeToken
    {
        $data = $this->skypeBotClient->createAuth2Token();
        if (!isset($data['access_token'])) {
            throw new SimpleSkypeBotException('Token did not create!');
        }
        /** @var SkypeToken $skypeToken */
        $skypeToken = $this->em->getRepository($this->tokenClass)->findOneBy(
            ['accessToken' => $data['access_token']]
        );

        if (empty($skypeToken)) {
            $token = new $this->tokenClass();
            $this->em->persist($token);
        }

        $token->setAccessToken($data['access_token']);
        $token->setApiType(SkypeToken::API_TYPE_AUTH2);
        $token->setTokenType($data['token_type']);
        $date = new \DateTime();
        $date->modify(sprintf('+%s seconds', $data['expires_in']));
        $token->setExpiresIn($date);

        $this->em->flush();

        return $token;
    }

    /**
     * @param Message $data
     * @return SkypeUser
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getSkypeUser(MessageDTO $message): SkypeUser
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
}

