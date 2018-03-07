<?php

namespace SimpleSkypeBot\Command;

use SimpleSkypeBot\Service\SkypeBotManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use SimpleUser\Service\UserManager;


class SendMessageCommand extends Command
{
    /**
     * @var SkypeBotManager
     */
    protected $skypeBotManager;

    public function __construct(?string $name = null, SkypeBotManager $skypeBotManager)
    {
        parent::__construct($name);
        $this->skypeBotManager = $skypeBotManager;
    }

    protected function configure()
    {
        $this
            ->setName('simply-skype-bot:send-message')
            ->addArgument('login', InputArgument::REQUIRED, 'Skype login.')
            ->addArgument('message', InputArgument::REQUIRED, 'Message example "You message here!".')
            ->setDescription('Simply send message to skype user.')
            ->setHelp('This command allows you to send message to skype user.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Send Message',
            '============',
            '',
        ]);

        $this->skypeBotManager->sendMessage(
            $input->getArgument('login'),
            $input->getArgument('message')
        );

    }

    /**
     * @return SkypeBotManager
     */
    public function getUserManager(): SkypeBotManager
    {
        return $this->skypeBotManager;
    }
}