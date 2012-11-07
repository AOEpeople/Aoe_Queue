<?php

require_once 'app/Mage.php';

class QueueTestcase extends PHPUnit_Framework_TestCase {


    public function setUp() {
        Mage::app('default');
    }
    
    public function testAddToQueue() {

    }

}