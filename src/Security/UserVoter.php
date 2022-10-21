<?php

namespace App\Security;

use App\Entity\Group;
use App\Entity\Subject;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
// эти строки были просто выдуманы: вы можете использовать что угодно
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const DELETE = 'delete';
    const FORCE_DELETE = 'force_delete';

    protected function supports(string $attribute, $subject): bool
    {
// если это не один из поддерживаемых атрибутов, возвращается false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE, self::FORCE_DELETE])) {
            return false;
        }

// голосовать только по объектам Post внутри этого избирателя
        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $auth = $token->getUser();

        if (!$auth instanceof User) {
// пользователь должен быть в системе; если нет - отказать в доступе
            return false;
        }

// вы знаете, что $subject - это объект Post, благодаря поддержке
        /** @var User $user */
        $user = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $auth);
            case self::EDIT:
                return $this->canEdit($user, $auth);
            case self::CREATE:
                return $this->canCreate($user, $auth);
            case self::DELETE:
                return $this->canDelete($user, $auth);
            case self::FORCE_DELETE:
                return $this->canForceDelete($user, $auth);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $user, User $auth): bool
    {
        if ($this->canEdit($user, $auth)) {
            return true;
        }

        if ($user->getRoles() == 'ROLE_STUDENT') {
            return true;
        }

        return false;
    }

    private function canEdit(User $user, User $auth): bool
    {
        if (in_array('ROLE_ADMIN',$auth->getRoles())) {
            return true;
        }

        if (in_array('ROLE_TEACHER',$auth->getRoles()) && $user->getGroups()->getId() == $auth->getGroups()->getId()) {
            return true;
        }

        return false;
    }

    private function canCreate(User $user, User $auth)
    {
        if (in_array('ROLE_ADMIN',$auth->getRoles())) {
            return true;
        }

        if (in_array('ROLE_TEACHER',$auth->getRoles())) {
            return true;
        }

        return false;
    }

    private function canDelete(User $user, User $auth)
    {
        return in_array('ROLE_ADMIN', $auth->getRoles());
    }

    private function canForceDelete(User $user, User $auth)
    {
        return in_array('ROLE_ADMIN', $auth->getRoles());
    }
}