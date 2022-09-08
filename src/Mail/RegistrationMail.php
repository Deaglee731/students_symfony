<?php

namespace App\Mail;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\User\UserInterface;

class RegistrationMail
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmailRegistration(UserInterface $user, $password)
    {
        $mail = (new TemplatedEmail())
            ->context(['user' => $user, 'password' => $password])
            ->from(new Address('admin@study.com', 'Education bot'))
            ->to($user->getEmail())
            ->subject("Ty for registration on education's portal")
            ->htmlTemplate('Mail/RegistrationMail.html.twig');

        $this->mailer->send($mail);
    }
}