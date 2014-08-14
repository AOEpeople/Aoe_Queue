<?php

/**
 * Message model
 *
 * @author Fabrizio Branca
 * @since 2012-11-07
 */
class Aoe_Queue_Model_Message extends Zend_Queue_Message {

    /**
     * Execute this message
     *
     * @return mixed
     */
    public function execute() {
        $data = unserialize($this->body);
        $callback = $this->parseCallbackString($data['model']);
        if (!is_array($data['parameters'])) {
            Mage::throwException('No parameters found.');
        }
        return call_user_func_array($callback, $data['parameters']);
    }

    /**
     * Parses a callback string (like used in Mage_Cron) "model/class::method"
     *
     * @param $callbackString
     * @return callback
     */
    public function parseCallbackString($callbackString) {
        if (!preg_match(Mage_Cron_Model_Observer::REGEX_RUN_MODEL, $callbackString, $run)) {
            Mage::throwException(Mage::helper('cron')->__('Invalid model/method definition, expecting "model/class::method".'));
        }
        if (!($model = Mage::getModel($run[1])) || !method_exists($model, $run[2])) {
            Mage::throwException(Mage::helper('cron')->__('Invalid callback: %s::%s does not exist', $run[1], $run[2]));
        }
        return array($model, $run[2]);
    }
}