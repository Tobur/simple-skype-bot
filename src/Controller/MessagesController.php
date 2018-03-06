<?php

namespace SimpleSkypeBot\Controller;

use Psr\Log\LoggerInterface;
use SimpleSkypeBot\Event\NewMessageEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessagesController extends Controller
{
    /**
     * @param Request $request
     * @param LoggerInterface $logger
     * @param EventDispatcher $eventDispatcher
     * @return Response
     */
    public function index(
        Request $request,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $authorization = $request->headers->get('authorization');
        $logger->debug('Authorization token: ' . $authorization);
        $data = json_decode($request->getContent(), true);
        //@TODO add validation check authorization token
        if (!empty($data) && !empty($authorization)) {
            $logger->debug('Dispath event ' . NewMessageEvent::NAME);
            $eventDispatcher->dispatch(
                NewMessageEvent::NAME,
                new NewMessageEvent($data)
            );
        }
        return new Response();
    }
}

