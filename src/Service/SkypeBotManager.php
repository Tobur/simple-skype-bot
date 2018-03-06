<?php

namespace SimpleSkypeBot\Service;

use Doctrine\ORM\EntityManagerInterface;
use SimpleSkypeBot\DTO\MessageDTO;

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

    public function __construct(
        SkypeBotClient $skypeBotClient,
        EntityManagerInterface $em,
        string $userClass,
        string $tokenClass
    ) {
        $this->skypeBotClient = $skypeBotClient;
        $this->em = $em;
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
        $this->skypeBotClient->sendMessage(
            $this->getToken(),
            $this->getSkypeUser($messageDTO),
            $messageDTO->getServiceUrl(),
            $message
        );
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
        $data = $this->bot->createAuth2Token();
        if (!isset($data['access_token'])) {
            throw new SimpleSkypeBotException('Token did not create!');
        }
        /** @var SkypeToken $skypeToken */
        $skypeToken = $this->em->getRepository($this->tokenClass)->findOneBy(
            ['access_token' => $data['access_token']]
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
    protected function getSkypeUser(Message $message): SkypeUser
    {
        $skypeUser = $this->em
            ->getRepository(SkypeUser::class)
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

