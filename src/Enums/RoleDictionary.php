<?php

namespace App\Enums;

enum RoleDictionary: string
{
    case ROLE_ADMIN = 'Админ';
    case ROLE_TEACHER = 'Учитель';
    case ROLE_STUDENT = 'Студент';

    public static function getRoles() {
        foreach (RoleDictionary::cases() as $case) {
            $roles[$case->value] = $case->name;
        }

        return $roles;
    }
}