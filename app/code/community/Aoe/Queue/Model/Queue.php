<?php

/**
 * Queue model
 *
 * @author Fabrizio Branca
 * @since 2012-11-07
 */
class Aoe_Queue_Model_Queue extends Zend_Queue {

    /**
     * Constructor
     */
    public function __construct($queueName='default') {

        if (empty($queueName)) {
            $queueName = 'default';
        }

        if (!is_string($queueName)) {
            Mage::throwException('Invalid queueName');
        }

        // reusing Magento config
        // TODO: introduce custom database handle for the actual connection later
        $dbConfig = Mage::getSingleton('core/resource')->getConnection('core_write')->getConfig();

        $config = array(
            'name' => $queueName,
            // TODO: is the Magento config object compatible to the expected driverOptions?
            'messageClass' => 'Aoe_Queue_Model_Message',
            'driverOptions' => array(
                'host'     => $dbConfig['host'],
                'port'     => '',
                'username' => $dbConfig['username'],
                'password' => $dbConfig['password'],
                'dbname'   => $dbConfig['dbname'],
                'type'     => $dbConfig['type']
            )
        );

        parent::__construct('Db', $config);
    }

    /**
     * Preventing devs from creating new queue with this class.
     *
     * @param string $name
     * @param null $timeout
     * @return false|void|Zend_Queue
     * @throws Exception
     */
    public function createQueue($name, $timeout = null) {
        throw new Exception('Creating a queue like this will create an object of class Zend_Queue instead of Aoe_Queue_Model_Queue. Please use the constructor instead.');
    }

    /**
     * @param $callback
     * @param array $parameterArray
     * @return Zend_Queue_Message
     */
    public function addTask($callback, array $parameterArray=array()) {
        $body = serialize(array(
            'model' => $callback,
            'parameters' => $parameterArray
        ));
        return $this->send($body);
    }

}