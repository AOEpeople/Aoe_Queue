<?php

class Aoe_Queue_Model_Cron {

    public function processQueue() {

        $starttime = microtime(true);
        $maxRuntime = 60; // TODO: read from configuration

        $queue = Mage::getModel('aoe_queue/queue'); /* @var $queue Aoe_Queue_Model_Queue */

        $queueNames = $queue->getQueues();

        $queues = array();
        $statistics = array();

        foreach ($queueNames as $queueName) {
            $tmp = Mage::getModel('aoe_queue/queue', $queueName); /* @var $tmp Aoe_Queue_Model_Queue */
            if ($tmp->count() == 0) {
                // $statistics[$queueName] = 'deleted';
                // $tmp->deleteQueue();
            } else {
                $queues[$queueName] = $tmp;
            }
        }

        // process
        while (((microtime(true) - $starttime) < $maxRuntime) && count($queues)) { // while there are queues with messages left
            foreach ($queues as $queueName => $queue) {
                $messages = $queue->receive(1);
                if (count($messages) > 0) {
                    foreach ($messages as $message) { /* @var $message Aoe_Queue_Model_Message */
                        $message->execute();
                        $queue->deleteMessage($message);
                        if (empty($statistics[$queueName])) { $statistics[$queueName] = 0; }
                        $statistics[$queueName]++;
                        if ((microtime(true) - $starttime) > $maxRuntime) {
                            return $statistics;
                        }
                    }
                } else {
                    // $queue->deleteQueue();
                    unset($queues[$queueName]);
                }
            }
        }

        return $statistics;
    }

}