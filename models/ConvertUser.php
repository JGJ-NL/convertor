<?php

class ConvertUser
{
    
    public static function isLoggedIn()
    {
        if (!empty($_SESSION['username'])) {
            return true;
        } else {
            return false;
        }
    }


    public static function authorized($user, $password)
    {
        // For this simple test/demo application, everyone is allowed to login,
        // as long as a user name and password are given that aren't identical.
        $allow = (!empty($user) && !empty($password) && $user!=$password) ? true : false;
        if ($allow) {
            $_SESSION['username'] = $user;
        }
        return $allow;
    }


    public static function username()
    {
        return self::isLoggedIn() ? $_SESSION['username'] : '???';
    }


    public static function logout()
    {
        unset($_SESSION['username']);
    }
    
}