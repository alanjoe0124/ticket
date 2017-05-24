<?php

class OurTicket_Util
{
    public static function validateEmail($email)
    {
        $len = strlen($email);
        if ($len < 4 || $len > 100) {
            throw new InvalidArgumentException('email minlength 4, maxlength 100');
        }

        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (!$email) {
            throw new InvalidArgumentException('invalid email');
        }

        return $email;
    }
    
    public static function getQuery($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }
}
