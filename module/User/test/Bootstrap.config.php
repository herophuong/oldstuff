<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
                'params' => array(
                    'path' => \UserTest\Bootstrap::findParentPath('module').'/../data/test.db',
                )
            )
        )
    ),
);