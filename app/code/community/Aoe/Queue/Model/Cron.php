<?php
/**
 * AOE Queue - Queue implementation for Magento based on Zend Queue
 *
 * Cron model handles the processeing of queued jobs
 * 
 * @category Mage
 * @package  Aoe_Queue
 * @author   Fabrizio Branca
 * @since    2012-11-09
 */
class Aoe_Queue_Model_Cron
{
    /**
     * Processes the queue, executing as necessary
     * @return array Statistics
     */
    public function processQueue(Mage_Cron_Model_Schedule $schedule = null)
    {
        $starttime  = microtime(true);
        $maxRuntime = Mage::getStoreConfig('system/aoe_queue/max_runtime');

        $queueNames = Mage::getSingleton('aoe_queue/queue')->getQueues();

        /* @var Aoe_Queue_Model_Queue[] $queue */
        $queues = array();
        foreach ($queueNames as $queueName) {
            /* @var Aoe_Queue_Model_Queue $queue */
            $queue = Mage::getModel('aoe_queue/queue', $queueName);
            if ($queue->count() > 0) {
                $queues[$queueName] = $queue;
            }
        }

        // process
        $statistics = array();
        // while there are queues with messages left
        while (((microtime(true) - $starttime) < $maxRuntime) && count($queues)) {
            foreach ($queues as $queueName => $queue) {
                /** @var Aoe_Queue_Model_Queue $queue */
                $messages = $queue->receive(1);
                if (count($messages) > 0) {
                    foreach ($messages as $message) {
                        /* @var $message Aoe_Queue_Model_Message */
                        try {
                            $message->execute();
                            $queue->deleteMessage($message);
                            if (empty($statistics[$queueName])) {
                                $statistics[$queueName] = 0;
                            }
                            $statistics[$queueName]++;
                            if ((microtime(true) - $starttime) > $maxRuntime) {
                                return $statistics;
                            }
                        } catch (Exception $e) {
                            /**
                             * Do not release the queue message on purpose
                             * This prevents a bad message from being constantly processed
                             * The message will become available again after the timeout
                             */
                            Mage::logException($e);
                            $statistics['__errors__'][$queueName][$message->message_id] = $e->getMessage();
                        }
                    }
                } else {
                    unset($queues[$queueName]);
                }
            }
        }

        if ($schedule && isset($statistics['__errors__']) && count($statistics['__errors__'])) {
            $schedule->setStatus(Mage_Cron_Model_Schedule::STATUS_ERROR);
        }

        return $statistics;
    }
}
