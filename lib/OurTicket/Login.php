<?php

class Login {

    public function check($data) {
        $paramArr = array('userName', 'pwd');
        foreach ($paramArr as $param) {
            if (!isset($data[$param])) {
                throw new InvalidArgumentException("Missing required $param");
            }
        }
        $data['userName'] = trim($data['userName']);
        $userNameLength = mb_strlen($data['userName'], 'UTF-8');
        if ($userNameLength > 50 || $userNameLength < 3) {
            throw new InvalidArgumentException('User name max length 50, min length 3');
        }
        $pwdLength = strlen($data['pwd']);
        if ($pwdLength > 40 || $pwdLength < 5) {
            throw new InvalidArgumentException('Password max length 40, min length 5');
        }

        $db = Db::getDb();
        $sql = 'SELECT id, name FROM user WHERE name = ? and pwd = ?';
        $stmt = $db->prepare($sql);
        $salt = 'acd806b0-d563-4824-907f-852f8f1003a5';
        $stmt->execute(array($data['userName'], md5($data['pwd'] . $salt)));
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        return $userRow;
    }

}
