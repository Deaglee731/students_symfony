<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UserRegisterEvent extends Event
{
    public function __construct(protected  User $user)
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}