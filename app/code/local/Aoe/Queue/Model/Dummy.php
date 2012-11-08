<?php

/**
 * Dummy model used in a unit test
 *
 * @author Fabrizio Branca
 * @since 2012-11-07
 */
class Aoe_Queue_Model_Dummy {

    /**
     * Wrapper for str_repeat
     *
     * @param $string
     * @param $repeat
     * @return string
     */
    public function test($string, $repeat) {
        return str_repeat($string, $repeat);
    }

}