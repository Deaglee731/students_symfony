<?php

namespace App\Mail;

use App\Entity\Score;
use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;

class StudentScoreMail
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendEmailScore(User $user)
    {
        $mail = (new TemplatedEmail())
            ->context(['user' => $user])
            ->from(new Address('admin@study.com', 'Education bot'))
            ->to($user->getEmail())
            ->subject("Your Scores " . $user->getName())
            ->htmlTemplate('Mail/StudentScores.html.twig');

        $this->mailer->send($mail);
    }
}