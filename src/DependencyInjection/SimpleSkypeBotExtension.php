<?php

namespace SimpleSkypeBot\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class SimpleSkypeBotExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('simply_skype_bot.client_id', $config['client_id']);
        $container->setParameter('simply_skype_bot.client_secret', $config['client_secret']);
        $container->setParameter('simply_skype_bot.bot_secret_key', $config['bot_secret_key']);

        $container->setParameter('simply_skype_bot.login_endpoint', $config['login_endpoint']);
        $container->setParameter('simply_skype_bot.bot_endpoint', $config['bot_endpoint']);
        $container->setParameter('simply_skype_bot.smba_endpoint', $config['smba_endpoint']);

        $container->setParameter('simply_skype_bot.token_class', $config['token_class']);
        $container->setParameter('simply_skype_bot.user_class', $config['user_class']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return 'simple_skype_bot';
    }
}
