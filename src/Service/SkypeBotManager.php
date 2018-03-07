<?php

namespace SimpleSkypeBot\Service;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SimpleSkypeBot\DTO\CoversationDTO;
use SimpleSkypeBot\DTO\MessageDTO;
use SimpleSkypeBot\Exceptions\SimpleSkypeBotException;
use SimpleSkypeBot\Model\SkypeToken;
use SimpleSkypeBot\Model\SkypeUser;

class SkypeBotManager
{
    /**
     * @var SkypeApiClient
     */
    protected $skypeApiClient;

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
     * @param SkypeApiClient $skypeApiClient
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     * @param string $userClass
     * @param string $tokenClass
     */
    public function __construct(
        SkypeApiClient $skypeApiClient,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        string $userClass,
        string $tokenClass
    ) {
        $this->skypeApiClient = $skypeApiClient;
        $this->em = $em;
        $this->tokenClass = $tokenClass;
        $this->userClass = $userClass;
        $this->logger = $logger;
    }

    /**
     * @param MessageDTO $message
     * @throws SimpleSkypeBotException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \SimpleSkypeBot\Exceptions\SImpleSkypeBotException
     */
    public function sendMessage(MessageDTO $messageDTO)
    {
        $this->logger->debug('Start send message', [self::class]);
        $result = $this->skypeApiClient->sendConnectorMessage(
            $this->getAuth2Token(),
            $messageDTO
        );

        $this->logger->debug('End send message', [self::class]);

        if (!empty($result['id'])) {
            $this->saveSkypeUser($messageDTO);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @return SkypeToken
     * @throws SimpleSkypeBotException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    protected function getAuth2Token(): SkypeToken
    {
        $data = $this->skypeApiClient->createAuth2Token();
        if (!isset($data['access_token'])) {
            throw new SimpleSkypeBotException('Auth2Token Token did not create!');
        }
        /** @var SkypeToken $skypeToken */
        $skypeToken = $this->em->getRepository($this->tokenClass)->findOneBy(
            [],
            ['id' => 'ASC']
        );

        if (empty($skypeToken)) {
            $skypeToken = new $this->tokenClass();
            $this->em->persist($skypeToken);
        }

        $date = new \DateTime();
        if (!is_null($skypeToken->getExpiresIn()) &&
            $date->getTimestamp() < $skypeToken->getExpiresIn()->getTimestamp()
        ) {
            return $skypeToken;
        }

        $skypeToken->setAccessToken($data['access_token']);
        $skypeToken->setTokenType($data['token_type']);
        $date->modify(sprintf('+%s seconds', $data['expires_in']));
        $skypeToken->setExpiresIn($date);

        $this->em->flush();

        return $skypeToken;
    }

    /**
     * @param MessageDTO $messageDTO
     */
    protected function saveSkypeUser(MessageDTO $messageDTO)
    {
        $skypeUser = $this->findUserInDbByMessageDTO($messageDTO);
        $skypeUser->setConversationId($messageDTO->getConversationId());
        $skypeUser->setSkypeLoginId($messageDTO->getFromId());
        $this->em->flush();
    }

    /**
     * @param string $skypeLogin
     * @param string $skypeLoginId
     * @return bool
     */
    public function setSkypeLogin(string $skypeLogin, string $skypeLoginId): bool
    {
        /** @var SkypeUser $skypeUser */
        $skypeUser = $this->em->getRepository($this->userClass)->findOneBy(
            ['skypeLoginId' => $skypeLoginId]
        );

        if (empty($skypeUser)) {
            return false;
        }

        $skypeUser->setSkypeLogin($skypeLogin);
        $this->em->flush();
        $this->logger->debug('Setup user login ' . $skypeLogin, [self::class]);

        return true;
    }

    /**
     * @param MessageDTO $messageDTO
     * @return SkypeUser
     */
    protected function findUserInDbByMessageDTO(MessageDTO $messageDTO): SkypeUser
    {
        $skypeUser = $this->em->getRepository($this->userClass)->findOneBy(
            [
                'skypeLoginId' => $messageDTO->getFromId()
            ],
            ['id' => 'ASC']
        );

        if ($skypeUser) {
            return $skypeUser;
        }

        $skypeUser = new $this->userClass();
        $this->em->persist($skypeUser);

        return $skypeUser;
    }
}

