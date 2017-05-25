<?php

class ManageController extends OurTicket_AdminAction
{
    public function indexAction()
    {
        $sql = 'SELECT  ticket.id AS id,
                    ticket.title,
                    ticket.domain,
                    ticket.time,
                    status.name AS status,
                    customer.email as customer
            FROM    ticket
                    INNER JOIN status ON ticket.status_id = status.id
                    INNER JOIN customer ON ticket.customer_id = customer.id
                    ORDER BY ticket.time DESC, ticket.id DESC'; // 1-pending 2-close
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $this->view->rows = $db->fetchAll($sql); 
        $this->setLayout('admin_layout');
    }
 }
