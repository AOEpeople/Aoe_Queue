<?php
/**
 * AOE Queue - Queue implementation for Magento based on Zend Queue
 *
 * Database adapter
 * 
 * @category Mage
 * @package  Aoe_Queue
 * @author   Lee Saferite <lee.saferite@aoe.com>
 * @since    2014-08-14
 */
class Aoe_Queue_Model_Adapter_Db extends Zend_Queue_Adapter_Db
{
    /**
     * Constructor
     *
     * @param  array|Zend_Config $options
     * @param  Zend_Queue|null   $queue
     *
     * @return self
     *
     * @throws Zend_Queue_Exception If requires options are not set
     */
    public function __construct($options, Zend_Queue $queue = null)
    {
        Zend_Queue_Adapter_AdapterAbstract::__construct($options, $queue);

        if (!isset($this->_options['options'][Zend_Db_Select::FOR_UPDATE])) {
            // turn off auto update by default
            $this->_options['options'][Zend_Db_Select::FOR_UPDATE] = false;
        }

        if (!is_bool($this->_options['options'][Zend_Db_Select::FOR_UPDATE])) {
            throw new Zend_Queue_Exception('Options array item: Zend_Db_Select::FOR_UPDATE must be boolean');
        }

        if (!isset($this->_options['dbAdapter']) || !$this->_options['dbAdapter'] instanceof Zend_Db_Adapter_Abstract) {
            throw new Zend_Queue_Exception('dbAdapter must be set');
        }

        if (!isset($this->_options['dbQueueTable']) || empty($this->_options['dbQueueTable'])) {
            throw new Zend_Queue_Exception('dbQueueTable must be set');
        }

        if (!isset($this->_options['dbMessageTable']) || empty($this->_options['dbMessageTable'])) {
            throw new Zend_Queue_Exception('dbMessageTable must be set');
        }

        $db = $this->_options['dbAdapter'];
        $queueTable = $this->_options['dbQueueTable'];
        $messageTable = $this->_options['dbMessageTable'];

        $this->_queueTable   = new Zend_Db_Table(array('db' => $db, 'name' => $queueTable, 'primary' => 'queue_id'));
        $this->_messageTable = new Zend_Db_Table(array('db' => $db, 'name' => $messageTable, 'primary' => 'message_id'));
    }
}
