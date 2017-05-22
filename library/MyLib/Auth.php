<?php

class ZendX_Auth implements Zend_Auth_Adapter_Interface {

    const SALT = 'acd806b0-d563-4824-907f-852f8f1003a5';

    protected $userName;
    protected $password;

    public function __construct($userName, $pwd) {
        
        if (!isset($userName)) {
            throw new InvalidArgumentException('Missing required userName');
        }

        if (!isset($pwd)) {
            throw new InvalidArgumentException('Missing required password');
        }

        $this->userName = trim($userName);
        $this->password = $pwd;
        $userNameLength = mb_strlen($this->userName , 'UTF-8');
        if ($userNameLength > 50 || $userNameLength < 3) {
            throw new InvalidArgumentException('User name max length 50, min length 3');
        }
        $pwdLength = strlen($pwd);
        if ($pwdLength > 40 || $pwdLength < 5) {
            throw new InvalidArgumentException('Password max length 40, min length 5');
        }
    }

    public function authenticate() {
        $uid = Zend_Db_Table_Abstract::getDefaultAdapter()->fetchOne(
                'SELECT id FROM user WHERE name = ? and pwd = ?', array($this->userName, md5($this->password . self::SALT))
        );

        if ($uid) {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $uid);
        }

        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, 0);
    }
    
    public function getUserName(){
        return $this->userName;
    }

}