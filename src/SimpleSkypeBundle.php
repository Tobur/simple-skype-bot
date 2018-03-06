<?php

namespace SimpleSkypeBot;

use SimpleSkypeBot\Exceptions\SimpleSkypeBotException;

class SimpleSkypeBotBundle
{
    public function getContainerExtension()
    {
        return new SimpleSkypeBotException();
    }
}

