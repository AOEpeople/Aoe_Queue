<?php
/**
 * AOE Queue - Queue implementation for Magento based on Zend Queue
 *
 * Queue model handles the instantiation and creation of a queue item
 * 
 * @category Mage
 * @package  Aoe_Queue
 * @author   Fabrizio Branca
 * @since    2012-11-07
 */
class Aoe_Queue_Model_Queue extends Zend_Queue
{
    /**
     * Constructor
     * 
     * @param  string $queueName
     * 
     * @throws Mage_Core_Exception If $queueName is not a string
     */
    public function __construct($queueName = 'default')
    {
        if (empty($queueName)) {
            $queueName = 'default';
        }

        if (!is_string($queueName)) {
            Mage::throwException('Invalid queueName');
        }

        /** @var Mage_Core_Model_Resource $resource */
        $resource = Mage::getModel('core/resource');

        /** @var Varien_Db_Adapter_Interface $connection */
        $connection = $resource->getConnection(Mage_Core_Model_Resource::DEFAULT_WRITE_RESOURCE);

        $config = array(
            'name'           => $queueName,
            'messageClass'   => 'Aoe_Queue_Model_Message',
            'dbAdapter'      => $connection,
            'dbQueueTable'   => $resource->getTableName('aoe_queue/queue'),
            'dbMessageTable' => $resource->getTableName('aoe_queue/message'),
        );

        parent::__construct($config);

        /** @var Aoe_Queue_Model_Adapter_Db $adapter */
        $adapter = Mage::getModel('aoe_queue/adapter_db', $this->getOptions());
        $this->setAdapter($adapter);
    }

    /**
     * Preventing devs from creating new queue with this class.
     *
     * @param  string   $name
     * @param  null|int $timeout
     *
     * @return void
     * @throws Exception
     */
    public function createQueue($name, $timeout = null)
    {
        throw new Exception(
            'Creating a queue like this will create an object of class Zend_Queue instead of Aoe_Queue_Model_Queue. '
            . 'Please use the constructor instead.'
        );
    }

    /**
     * Adds a task to the queue
     * 
     * @param string $callback       Dev class code and method, e.g. "model/class::method"
     * @param array  $parameterArray Parameters to pass in for the task
     *
     * @return Zend_Queue_Message
     */
    public function addTask($callback, array $parameterArray = array())
    {
        $body = Zend_Json::encode(
            array(
                'model'      => $callback,
                'parameters' => $parameterArray
            )
        );

        return $this->send($body);
    }
}
