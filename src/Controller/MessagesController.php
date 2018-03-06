<?php

namespace SimpleSkypeBot\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MessagesController extends Controller
{
    /**
     * @param Request $request
     * @param LoggerInterface $logger
     */
    public function index(Request $request, LoggerInterface $logger)
    {
    }
}

