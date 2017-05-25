<?php

class OurTicket_Login
{
    const SALT = 'acd806b0-d563-4824-907f-852f8f1003a5';

    public static function doLogin(array $data)
    {
        $requiredKeys = array('name', 'pwd');
        foreach ($requiredKeys as $key) {
            if (!isset($data[$key])) {
                throw new InvalidArgumentException("missing required key $key");
            }
        }

        $data['name'] = trim($data['name']);
        $len = mb_strlen($data['name'], 'UTF-8');
        if ($len == 0 || $len > 100) {
            throw new InvalidArgumentException('name required and maxlength is 100');
        }

        $len = strlen($data['pwd']);
        if ($len < 5 || $len > 40) {
            throw new InvalidArgumentException('pwd minlength is 5, maxlength is 40');
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = 'SELECT id, name FROM user WHERE name = ? and pwd = ?';

        return $db->fetchRow($sql, array($data['name'], md5($data['pwd'] . self::SALT)));
    }
}
