<?php

namespace SimpleSkypeBot\Event;

use SimpleSkypeBot\DTO\MessageDTO;
use Symfony\Component\EventDispatcher\Event;

class NewMessageEvent extends Event
{
    const NAME = 'new.message.event';

    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): MessageDTO
    {
        return new MessageDTO($this->data);
    }
}

