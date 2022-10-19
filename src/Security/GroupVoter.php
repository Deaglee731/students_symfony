<?php

namespace App\Security;

use App\Entity\Group;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GroupVoter extends Voter
{
// эти строки были просто выдуманы: вы можете использовать что угодно
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const DELETE = 'detele';

    protected function supports(string $attribute, $subject): bool
    {
// если это не один из поддерживаемых атрибутов, возвращается false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE])) {
            return false;
        }

// голосовать только по объектам Post внутри этого избирателя
        if (!$subject instanceof Group) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
// пользователь должен быть в системе; если нет - отказать в доступе
            return false;
        }

// вы знаете, что $subject - это объект Post, благодаря поддержке
        /** @var Group $group */
        $group = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($group, $user);
            case self::EDIT:
                return $this->canEdit($group, $user);
            case self::CREATE:
                return $this->canCreate($group, $user);
            case self::DELETE:
                return $this->canDelete($group, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Group $group, User $user): bool
    {
        if ($this->canEdit($group, $user)) {
            return true;
        }

        if ($user->getRoles() == 'ROLE_STUDENT' && ($group->getId() == $user->getGroups()->getId())) {
            return true;
        }

        return false;
    }

    private function canEdit(Group $group, User $user): bool
    {
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            return true;
        }

        if (in_array('ROLE_TEACHER',$user->getRoles()) && $group->getId() == $user->getGroups()->getId()) {
            return true;
        }

        return false;
    }

    private function canCreate(Group $group, User $user)
    {
        return $user->getRoles() == 'ROLE_ADMIN';
    }

    private function canDelete(Group $group, User $user)
    {
        return false;
    }
}