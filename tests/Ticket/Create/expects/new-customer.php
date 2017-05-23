<?php

return array(
    'customer' => array(
        array('id' => 1, 'email' => 'test001@163.com'),
        array('id' => 2, 'email' => 'test002@163.com')
    ),
    'ticket'   => array(
        array(
            'id'          => 1,
            'title'       => 'how to write blog?',
            'description' => 'RT. how to write blog?',
            'customer_id'        => 2,
            'domain'      => 'ourblog.dev',
            'status_id'      => 1
        )
    )
);
