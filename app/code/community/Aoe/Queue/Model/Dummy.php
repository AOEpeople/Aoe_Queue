<?php
/**
 * AOE Queue - Queue implementation for Magento based on Zend Queue
 *
 * Dummy model used in a unit test
 * 
 * @category Mage
 * @package  Aoe_Queue
 * @author   Fabrizio Branca
 * @since    2012-11-08
 */
class Aoe_Queue_Model_Dummy
{
    /**
     * Wrapper for str_repeat
     *
     * @param  string $string
     * @param  int    $repeat
     * @return string
     */
    public function test($string, $repeat)
    {
        return str_repeat($string, $repeat);
    }
}
