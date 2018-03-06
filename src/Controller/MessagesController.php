<?php

namespace SimpleSkypeBot\Controller;

use Psr\Log\LoggerInterface;
use SimpleSkypeBot\Event\NewMessageEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

class MessagesController extends Controller
{
    /**
     * @param Request $request
     * @param LoggerInterface $logger
     */
    public function index(
        Request $request,
        LoggerInterface $logger,
        EventDispatcher $eventDispatcher
    ) {
        $authorization = $request->headers->get('authorization');
        $data = json_decode($request->getContent(), true);
        //@TODO add check authorization token
        if (!empty($data) && !empty($authorization)) {
            $eventDispatcher->dispatch(
                NewMessageEvent::NAME,
                new NewMessageEvent($data)
            );
        }

        return new Response();
    }
}

