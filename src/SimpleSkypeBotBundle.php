<?php

namespace SimpleSkypeBot;

use SimpleSkypeBot\DependencyInjection\SimpleSkypeBotExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SimpleSkypeBotBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new SimpleSkypeBotExtension();
    }
}

