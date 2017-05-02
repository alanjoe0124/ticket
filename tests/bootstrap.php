<?php

define('TICKET_LIB', __DIR__ . '/../lib');
define('DB_NAME', 'ticket_test');

class Ticket_DbUnit_ArrayDataSet extends PHPUnit_Extensions_Database_DataSet_AbstractDataSet {

    protected $tables = array();

    public function __construct(array $data) {
        foreach ($data as $tableName => $rows) {
            $columns = array();
            if (isset($rows[0])) {
                $columns = array_keys($rows[0]);
            }

            $metaData = new PHPUnit_Extensions_Database_DataSet_DefaultTableMetaData($tableName, $columns);
            $table = new PHPUnit_Extensions_Database_DataSet_DefaultTable($metaData);

            foreach ($rows AS $row) {
                $table->addRow($row);
            }

            $this->tables[$tableName] = $table;
        }
    }

    protected function createIterator($reverse = false) {
        return new PHPUnit_Extensions_Database_DataSet_DefaultTableIterator($this->tables, $reverse);
    }

    public function getTable($tableName) {
        if (!isset($this->tables[$tableName])) {
            throw new InvalidArgumentException("$tableName is not a table in the current database.");
        }

        return $this->tables[$tableName];
    }

}

abstract class Ticket_Database_TestCase extends PHPUnit_Extensions_Database_TestCase {

    protected static $conn;

    public function getConnection() {
        if (!self::$conn) {
            $pdo = new PDO('mysql:host=localhost;port=3306;dbname=ticket_test;charset=utf8', 'root', '');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn = $this->createDefaultDBConnection($pdo, 'ticket_test');
        }
        return self::$conn;
    }

    protected function createArrayDataSet(array $data) {
        return new Ticket_DbUnit_ArrayDataSet($data);
    }

}
