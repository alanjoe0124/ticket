<?php

class Ticket_CloseTest extends Ticket_Database_TestCase {

    public function getDataSet() {
        return $this->createArrayDataSet(array(
                    'customer' => array(
                        array('id' => 1, 'email' => 'test001@163.com')
                    ),
                    'ticket' => array(
                        array(
                            'id'            => 1,
                            'title'         => 'how to write blog?',
                            'description'   => 'RT. how to write blog?',
                            'customer_id'          => 1,
                            'domain'        => 'ourblog.dev',
                            'status_id'        => 1
                        )
                    )
        ));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Customer Email and ticket id not related
     */
    public function testCloseUnrelatedTicket() {

        $ticket = new MyLib_Ticket();
        $ticket->close('test001@163.com',2);
    }

    public function testCloseTicket() {
        $ticket = new MyLib_Ticket();
        $ticket->close('test001@163.com',1);

        $expectedDataSet = $this->createArrayDataSet(include __DIR__ . '/expect.php');

        $dataSet = $this->getConnection()->createDataSet(array('customer', 'ticket'));
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter($dataSet);
        $filterDataSet->setExcludeColumnsForTable('ticket', array('time'));

        $this->assertDataSetsEqual($expectedDataSet, $filterDataSet);
    }

}
