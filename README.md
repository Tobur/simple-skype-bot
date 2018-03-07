Install 

```
composer require tobur/simple-skype-bot
```
```
SimpleSkypeBot\SimpleSkypeBotBundle::class => ['all' => true]
```
```
simple_skype_bot:
  token_class: 'App\Entity\SkypeToken'
  user_class: 'App\Entity\SkypeUser'
  client_id: 'some client id'
  client_secret: 'some secret'
```

You should create application and bot on https://dev.botframework.com/bots/new.

Put your endpoint to the main route.

```
skype_endpoint:
    resource: '@SimpleSkypeBotBundle/Resources/config/routing.yaml'
    prefix: /
```

For your bot you should setup api endpoint. For develop you can use http tunnel with https://ngrok.com/. 

```
./ngrok http 80
```

Like example for dev you can use: https://78bec8dc.ngrok.io/api/messages
Add you bot to skype.

Example how to handle messages

```
<?php

namespace App\Subscriber;

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
            NewMessageEvent::NAME => 'handleNewMessage'
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
        $messageDTO->setText('Hello man!');
        $this->botManager->sendMessage($messageDTO);
    }
}
```

Some default skype command, put it to the skype with your bot:
```
.save my_skype_login
```
Than you can use:

```
php bin/console simply-skype-bot:send-message my_skype_login Hi
```
