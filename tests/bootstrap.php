<?php

define('DB_NAME', 'ticket_test');

include __DIR__ . '/../lib/OurTicket/Db.php';
include __DIR__ . '/../lib/OurTicket/Ticket.php';

class Ticket_DbUnit_ArrayDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet 
{
    protected $tables = array();

    public function __construct(array $data)
    {
        foreach ($data as $tableName => $rows) {
            $columns = array();
            if (isset($rows[0])) {
                $columns = array_keys($rows[0]);
            }

            $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
            $table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);
            foreach ($rows as $row) {
                $table->addRow($row);
            }
            $this->tables[$tableName] = $table;
        }
    }

    protected function createIterator($reverse = false)
    {
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
    }

    public function getTable($tableName)
    {
        if (!isset($this->tables[$tableName])) {
            throw new InvalidArgumentException("$tableName is not a table in the current database.");
        }

        return $this->tables[$tableName];
    }
}

abstract class Ticket_Database_TestCase extends PHPUnit_Extensions_Database_TestCase 
{
    protected static $conn;

    public function getConnection()
    {
        if (!self::$conn) {
            self::$conn = $this->createDefaultDBConnection(OurTicket_Db::getDb(), 'ticket_test');
        }
        return self::$conn;
    }

    protected function createArrayDataSet(array $data)
    {
        return new Ticket_DbUnit_ArrayDataSet($data);
    }
}
