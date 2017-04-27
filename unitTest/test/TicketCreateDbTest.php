<?php

include __DIR__ . '/../include/MyApp_DbUnit_ArrayDataSet.php';

class TicketCreateDbTest extends PHPUnit_Extensions_Database_TestCase {

    public function getConnection() {
        $pdo = new PDO(
                'mysql:host=localhost;port=3306;dbname=ticket_test;charset=utf8', 'root', ''
        );
        return $this->createDefaultDBConnection($pdo, 'ticket_test');
    }

    public function getDataSet() {
        return new MyApp_DbUnit_ArrayDataSet(array(
            'customer' => array(
                array('id' => 1, 'name' => 'test@163.com')
            ),
            'ticket' => array()
        ));
    }

    public function test_get_user_id() {
        $ticketDb = new TicketCreateDb();
        $userId = $ticketDb->get_user_id('test@163.com');
        $this->assertEquals('1', $userId);
    }

    public function test_create_new_customer() {
        $ticketDb = new TicketCreateDb();
        $this->assertTrue($ticketDb->create_new_customer('test_new@163.com'));
    }

    public function test_create_new_ticket() {
        $ticketDb = new TicketCreateDb();
        $this->assertTrue($ticketDb->create_new_ticket('test_title', 'test_desc', 1));
    }

}
