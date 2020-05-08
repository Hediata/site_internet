<?php


namespace App;


use App\Entity\Utilisateurs;

abstract class ModerationAccess
{
    private static $accessLevel = 1;
    private static $whiteList = [
        'gashmob',
    ];

    /**
     * @param $user Utilisateurs
     * @return boolean
     */
    public static function haveAccess($user)
    {
        if ($user->getSection()) {
            return (in_array($user->getLogin(), self::$whiteList)) || ($user->getGrade()->getNiveau() <= self::$accessLevel);
        } else {
            return in_array($user->getLogin(), self::$whiteList);
        }
    }
}