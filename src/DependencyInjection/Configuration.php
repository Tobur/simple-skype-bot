<?php

namespace SimpleSkypeBot\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('simple_skype_bot');
        $rootNode
            ->children()
                ->scalarNode('skype_token_class')
                    ->isRequired()
                ->end()
                ->scalarNode('skype_user_class')
                    ->isRequired()
                ->end()
                ->scalarNode('client_id')
                    ->isRequired()
                ->end()
                ->scalarNode('client_secret')
                    ->isRequired()
                ->end()
                ->scalarNode('bot_secret_key')
                    ->isRequired()
                ->end()
                ->scalarNode('login_endpoint')
                    ->defaultValue('https://login.microsoftonline.com')
                    ->isRequired()
                ->end()
                ->scalarNode('bot_endpoint')
                    ->defaultValue('https://directline.botframework.com')
                    ->isRequired()
                ->end()
                ->scalarNode('smba_endpoint')
                    ->defaultValue('https://smba.trafficmanager.net')
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
