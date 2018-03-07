<?php

namespace SimpleSkypeBot\Command;

use Doctrine\ORM\EntityManagerInterface;
use SimpleSkypeBot\DTO\MessageDTO;
use SimpleSkypeBot\Model\SkypeUser;
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

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var string
     */
    protected $userClass;

    /**
     * SendMessageCommand constructor.
     * @param null|string|null $name
     * @param SkypeBotManager $skypeBotManager
     * @param EntityManagerInterface $entityManager
     * @param string $userClass
     */
    public function __construct(
        ?string $name = null,
        SkypeBotManager $skypeBotManager,
        EntityManagerInterface $entityManager,
        string $userClass
    ) {
        parent::__construct($name);

        $this->skypeBotManager = $skypeBotManager;
        $this->entityManager = $entityManager;
        $this->userClass = $userClass;
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

        /** @var SkypeUser $skypeUser */
        $skypeUser = $this->getEntityManager()->getRepository($this->userClass)
            ->findOneBy(['skypeLogin' => $input->getArgument('login')]);

        if (!$skypeUser) {
           $output->writeln([
                '',
                'User with such login did not found!',
                ''
           ]);
        }

        $messageDTO = new MessageDTO([]);
        $messageDTO->setConversationId($skypeUser->getConversationId());
        $messageDTO->setText($input->getArgument('message'));
        $messageDTO->setRecipientId($skypeUser->getSkypeLoginId());

        $this->skypeBotManager->sendMessage($messageDTO);

        $output->writeln([
            '',
            'Successfully send',
            ''
        ]);
    }

    /**
     * @return SkypeBotManager
     */
    public function getUserManager(): SkypeBotManager
    {
        return $this->skypeBotManager;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }
}