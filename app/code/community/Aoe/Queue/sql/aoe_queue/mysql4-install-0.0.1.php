<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();

$this->getConnection()->dropTable($this->getTable('aoe_queue/queue'));
$this->getConnection()->dropTable($this->getTable('aoe_queue/message'));

$queue = $this->getConnection()->newTable($this->getTable('aoe_queue/queue'));
$queue->addColumn(
    'queue_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'identity' => true,
        'primary'  => true,
        'unsigned' => true,
        'nullable' => false,
    )
);
$queue->addColumn(
    'queue_name',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    100,
    array(
        'nullable' => false,
    )
);
$queue->addColumn(
    'timeout',
    Varien_Db_Ddl_Table::TYPE_SMALLINT,
    null,
    array(
        'unsigned' => true,
        'nullable' => false,
        'default'  => 30
    )
);
$this->getConnection()->createTable($queue);

$message = $this->getConnection()->newTable($this->getTable('aoe_queue/message'));
$message->addColumn(
    'message_id',
    Varien_Db_Ddl_Table::TYPE_BIGINT,
    null,
    array(
        'identity' => true,
        'primary'  => true,
        'unsigned' => true,
        'nullable' => false,
    )
);
$message->addColumn(
    'queue_id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'unsigned' => true,
        'nullable' => false,
    )
);
$message->addColumn(
    'handle',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    32,
    array(
        'nullable' => true,
    )
);
$message->addColumn(
    'body',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    8192,
    array(
        'nullable' => false,
    )
);
$message->addColumn(
    'md5',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    32,
    array(
        'nullable' => false,
    )
);
$message->addColumn(
    'timeout',
    Varien_Db_Ddl_Table::TYPE_DECIMAL,
    array(14, 4),
    array(
        'unsigned' => true,
        'nullable' => true,
    )
);
$message->addColumn(
    'created',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'unsigned' => true,
        'nullable' => false,
    )
);
$message->addForeignKey(
    $this->getFkName('aoe_queue/message', 'queue_id', 'aoe_queue/queue', 'queue_id'),
    'queue_id',
    $this->getTable('aoe_queue/queue'),
    'queue_id',
    Varien_Db_Ddl_Table::ACTION_CASCADE,
    Varien_Db_Ddl_Table::ACTION_CASCADE
);
$this->getConnection()->createTable($message);

$this->endSetup();
