<?php

namespace SimpleSkypeBot\Subscriber;

use Doctrine\ORM\EntityManagerInterface;
use SimpleSkypeBot\DTO\MessageDTO;
use SimpleSkypeBot\Event\NewMessageEvent;
use SimpleSkypeBot\Exceptions\SimpleSkypeBotException;
use SimpleSkypeBot\Service\SkypeBotManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SkypeSubscriber implements EventSubscriberInterface
{
    /**
     * @var SkypeBotManager
     */
    protected $botManager;

    /**
     * @param SkypeBotManager $botManager
     */
    public function __construct(SkypeBotManager $botManager) {
        $this->botManager = $botManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            NewMessageEvent::NAME => ['handleNewMessage', 10000]
        ];
    }

    /**
     * @param NewMessageEvent $event
     * @throws SImpleSkypeBotException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \SimpleSkypeBot\Service\SimpleSkypeBotException
     */
    public function handleNewMessage(NewMessageEvent $event)
    {
        /** @var MessageDTO $messageDTO */
        $messageDTO = $event->getData();
        $text = $messageDTO->getText();
        if ($text[0] !== '.') {
            return;
        }

        list ($command, $skypeLogin) = explode(' ', $text);
        if ($command !== '.save') {
            return;
        }

        $result = $this->botManager->setSkypeLogin($skypeLogin, $messageDTO->getFromId());
        if ($result) {
            $messageDTO->setText('Your login saved!');
            $this->botManager->sendMessage($messageDTO);
        } else {
            $messageDTO->setText('Something wrong, we do not save your login!');
        }
        $event->stopPropagation();
    }
}

