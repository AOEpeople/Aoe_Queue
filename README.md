# Aoe_Queue

## How to run unit tests

    cd <MagentoDocRoot>

    # Get phpunit
    wget http://pear.phpunit.de/get/phpunit.phar
    chmod +x phpunit.phar

    # Run tests
    ./phpunit.phar tests/Aoe_Queue/QueueTestcase.php

## Adding a task to the queue

    $queue = Mage::getModel('aoe_queue/queue'); /* @var $queue Aoe_Queue_Model_Queue */
    $queue->addTask('aoe_queue/dummy::test', array('-+', '5'));

## Processing the queue

    $queue = Mage::getModel('aoe_queue/queue'); /* @var $queue Aoe_Queue_Model_Queue */
    $messages = $queue->receive(5); /* @var $messages Zend_Queue_Message_Iterator */
    foreach ($messages as $message) { /* @var $message Aoe_Queue_Model_Message */
        $message->execute();
    }

