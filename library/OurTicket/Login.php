<?php

class OurTicket_Login implements Zend_Auth_Adapter_Interface
{
    const SALT = 'acd806b0-d563-4824-907f-852f8f1003a5';
    protected $userName;
    protected $password;

    public function __construct($userName, $pwd) {
        if (!isset($userName)) {
            throw new InvalidArgumentException('missing required userName');
        }
        if (!isset($pwd)) {
            throw new InvalidArgumentException('missing required password');
        }
        $this->userName = trim($userName);
        $this->password = $pwd;
        $len = mb_strlen($this->userName , 'UTF-8');
        if ($len == 0 || $len > 100) {
            throw new InvalidArgumentException('name required and maxlength is 100');
        }
        $len = strlen($pwd);
        if ($len < 5 || $len > 40) {
            throw new InvalidArgumentException('pwd minlength is 5, maxlength is 40');
        }
    }

    public function authenticate() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = 'SELECT id FROM user WHERE name = ? and pwd = ?';
        $uid = $db->fetchOne($sql, array($this->userName, md5($this->password . self::SALT))
        );
        if ($uid) {
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $uid);
        }
        return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, 0);
    }

    public function getUserName()
    {
        return $this->userName;
    }
}
