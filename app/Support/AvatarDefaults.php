<?php

namespace App\Support;

final class AvatarDefaults
{
    public const USER = 'avatars/default-avatar.svg';

    public const GROUP = 'avatars/default-group-avatar.svg';

    /**
     * @return array<int, string>
     */
    public static function reservedPaths(): array
    {
        return [
            self::USER,
            self::GROUP,
        ];
    }
}
