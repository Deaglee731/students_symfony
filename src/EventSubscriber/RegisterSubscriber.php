<?php

namespace App\EventSubscriber;

use App\Event\UserRegisterEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class RegisterSubscriber implements EventSubscriberInterface
{
    public function onUserRegisterEvent(UserRegisterEvent $event): void
    {
            //event registrations users
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserRegisterEvent::class => 'onUserRegisterEvent',
        ];
    }
}
