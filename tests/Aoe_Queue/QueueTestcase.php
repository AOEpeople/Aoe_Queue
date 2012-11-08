<?php

// TODO: this should go into a bootstrap file
require_once 'app/Mage.php';
Mage::app('default');

/**
 * Strange problem in PHPUnit when used with Magento. This class doesn't exist but the Magento Autoloader tries to
 * load it (Maybe a class_exists() somewhere?) Anyways, this makes the Magento Autoloader fail. So here we are...
 */
class PHPUnit_Extensions_Story_TestCase {}

/**
 * Queue testcase
 *
 * @author Fabrizio Branca
 * @since 2012-11-07
 */
class QueueTestcase extends PHPUnit_Framework_TestCase {

    /**
     * @var Aoe_Queue_Model_Queue
     */
    protected $_queue;

    /**
     * @var string
     */
    protected $_queueName;

    public function setUp() {
        $this->_queueName = 'testqueue_' . time();

        $queue = Mage::getModel('aoe_queue/queue', $this->_queueName); /* @var $queue Aoe_Queue_Model_Queue */
        $this->_queue = $queue;
    }

    public function tearDown() {
        $this->_queue->deleteQueue();
    }

    public function testAddToQueueAndExecute() {

        // adding task
        $this->_queue->addTask('aoe_queue/dummy::test', array('-+', '5'));

        $this->assertEquals(1, $this->_queue->count());

        $messages = $this->_queue->receive(1); /* @var $messages Zend_Queue_Message_Iterator */
        foreach ($messages as $message) { /* @var $message Aoe_Queue_Model_Message */
            $actualResult = $message->execute();
            $expectedResult = Mage::getModel('aoe_queue/dummy')->test('-+', '5');
            $this->assertEquals($expectedResult, $actualResult);
        }
    }

}