<?php

namespace SimpleSkypeBot\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use SimpleSkypeBot\DTO\MessageDTO;
use SimpleSkypeBot\Event\NewMessageEvent;
use SimpleSkypeBot\Service\SkypeApiClient;
use SimpleSkypeBot\Service\SkypeBotManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessagesController extends Controller
{
    /**
     * @TODO add validation check authorization token
     * @param Request $request
     * @param LoggerInterface $logger
     * @param EventDispatcher $eventDispatcher
     * @return Response
     */
    public function index(
        Request $request,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher,
        SkypeApiClient $skypeApiClient,
        SkypeBotManager $skypeBotManager
    ) {
        $authorization = $request->headers->get('authorization');
        $logger->debug('Authorization token: ' . $authorization);

        $data = json_decode($request->getContent(), true);
        $logger->debug(print_r($data, true), [static::class]);


        if (array_key_exists('type', $data) &&
            !empty($authorization) &&
            $data['type'] === 'message' &&
            $data['channelId'] === 'skype'
        ) {
            $logger->debug('Dispath event ' . NewMessageEvent::NAME);
            $eventDispatcher->dispatch(
                NewMessageEvent::NAME,
                new NewMessageEvent($data)
            );
        }

        return new JsonResponse($data);
    }
}

