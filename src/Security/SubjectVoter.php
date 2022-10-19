<?php

namespace App\Security;

use App\Entity\Group;
use App\Entity\Subject;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SubjectVoter extends Voter
{
// эти строки были просто выдуманы: вы можете использовать что угодно
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
// если это не один из поддерживаемых атрибутов, возвращается false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::CREATE, self::DELETE])) {
            return false;
        }

// голосовать только по объектам Post внутри этого избирателя
        if (!$subject instanceof Subject) {
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
        /** @var Subject $subject */
        $subj = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subj, $user);
            case self::EDIT:
                return $this->canEdit($subj, $user);
            case self::CREATE:
                return $this->canCreate($subj, $user);
            case self::DELETE:
                return $this->canDelete($subj, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Subject $subj, User $user): bool
    {
        if ($this->canEdit($subj, $user)) {
            return true;
        }

        if ($user->getRoles() == 'ROLE_STUDENT') {
            return true;
        }

        return false;
    }

    private function canEdit(Subject $subj, User $user): bool
    {
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            return true;
        }

        if (in_array('ROLE_TEACHER',$user->getRoles())) {
            return true;
        }

        return false;
    }

    private function canCreate(Subject $subj, User $user)
    {
        if (in_array('ROLE_ADMIN',$user->getRoles())) {
            return true;
        }

        if (in_array('ROLE_TEACHER',$user->getRoles())) {
            return true;
        }

        return false;
    }

    private function canDelete(Subject $subj, User $user)
    {
        return in_array('ROLE_ADMIN', $user->getRoles());
    }
}