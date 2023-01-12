<?php

namespace App\Entity;

class UserStaticStorage
{
    public const USER_ROLE_USER = 'ROLE_USER';
    public const USER_ROLE_ADMIN = 'ROLE_ADMIN';

    public static function getUserRolesChoises(): array
    {
        return [
            self::USER_ROLE_USER => 'Admin'
        ];
    }
}