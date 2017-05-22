<?php

class MyLib_Ticket
{

    private $ticketId;
    private $userId;
    private $userType;

    public function __construct($ticketId = NULL, $userId = NULL, $userType = NULL)
    {
        $this->ticketId = $ticketId;
        $this->userId = $userId;
        $this->userType = $userType;
    }

    public function create(array $data)
    {
        $paramArr = array('title', 'description', 'email', 'domain');
        foreach ($paramArr as $param) {
            if (!isset($data[$param])) {
                throw new InvalidArgumentException("Required $param is missing");
            }
        }

        $titleLength = mb_strlen($data['title'], "UTF-8");
        if ($titleLength > 500 || $titleLength < 1) {
            throw new InvalidArgumentException('Title max length 500, min length 1');
        }
        if (strlen($data['description']) > 64000) {
            throw new InvalidArgumentException('Max description is 64000');
        }
        $emailLength = strlen($data['email']);
        if ($emailLength > 100 || $emailLength < 4) {
            throw new InvalidArgumentException('Email min length 4, max length 100');
        }
        $data['email'] = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        if (!$data['email']) {
            throw new InvalidArgumentException('Email invalid');
        }
        if ($data['domain'] != 'ourblog.dev') {
            throw new InvalidArgumentException('Domain invalid');
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $customerId = $db->fetchOne('SELECT id FROM customer WHERE email = ?', array($data['email']));
        $db->beginTransaction();
        try {
            if (!$customerId) {
                $db->insert('customer', array('email' => $data['email']));
                $customerId = $db->lastInsertId();
            }
            $db->insert('ticket', array(
                'title'         => $data['title'],
                'description'   => $data['description'],
                'customer_id'   => $customerId,
                'domain'        => $data['domain']
                    )
            );
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function commentPost($comment)
    {
        if (!isset($this->userId)) {
            throw new InvalidArgumentException('Missing required userId');
        }
        if (!isset($this->ticketId)) {
            throw new InvalidArgumentException('Missing required ticketId');
        }
        if (!isset($comment)) {
            throw new InvalidArgumentException('Missing required comment');
        }
        $commentLength = strlen(trim($comment));
        if ($commentLength > 64000 || $commentLength == 0) {
            throw new InvalidArgumentException('Comment max length 64000 and not empty');
        }
        $this->ticketId = filter_var($this->ticketId, FILTER_VALIDATE_INT, array(
            'options' => array('min_range' => 1)
        ));
        if (!$this->ticketId) {
            throw new InvalidArgumentException('Invalid ticket id');
        }

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->insert('comment', array(
            'content'   => $comment,
            'user'      => $this->userId,
            'ticket_id' => $this->ticketId,
            'user_type' => $this->userType));
        // user_type ( 1 = > table(`customer`) , 2 => table (`user`)
        return $this->ticketId;
    }

    public function close($customerEmail, $ticketId)
    {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $select = $db->select()->from('ticket', array('status'))
                ->joinInner('customer', 'ticket.user = customer.id')
                ->where("customer.name = ? AND ticket.id = $ticketId");

        $stmt = $select->query(PDO::FETCH_ASSOC, array($customerEmail));
        $status = $stmt->fetchColumn();

        if (!$status) {
            throw new InvalidArgumentException('Customer Email and ticket id not related');
        }

        if ($status != 2) { // ticket status ( 1 => pending, 2 => close ) 
            $db->update('ticket', array('status' => 2), "id = $ticketId");
        }
    }

}
