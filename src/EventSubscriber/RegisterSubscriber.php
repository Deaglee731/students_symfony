<?php

namespace App\EventSubscriber;

use App\Event\UserRegisterEvent;
use App\Mail\RegistrationMail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class RegisterSubscriber implements EventSubscriberInterface
{
    public  $registrationMail;

    public function __construct(RegistrationMail $registrationMail)
    {
        $this->registrationMail = $registrationMail;
    }

    public function onUserRegisterEvent(UserRegisterEvent $event): void
    {
        $this->registrationMail->sendEmailRegistration($event->getUser(), $event->getPassword());
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisterEvent::class => 'onUserRegisterEvent',
        ];
    }
}
