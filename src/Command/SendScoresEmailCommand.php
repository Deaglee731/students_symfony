<?php

namespace App\Command;

use App\Mail\StudentScoreMail;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:send-score',
    description: 'Creates a new user.',
    aliases: ['app:send-score'],
    hidden: false
)]
class SendScoresEmailCommand extends Command
{
    protected StudentScoreMail $mail;
    protected UserRepository $userReposotory;

    public function __construct(StudentScoreMail $mail, UserRepository $userRepository)
    {
        $this->userReposotory = $userRepository;
        $this->mail = $mail;
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->userReposotory->findAll();

        foreach ($users as $user) {
            $this->mail->sendEmailScore($user);
        }

        return Command::SUCCESS;
    }
}