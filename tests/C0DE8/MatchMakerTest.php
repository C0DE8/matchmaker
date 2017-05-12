<?php

use PHPUnit\Framework\TestCase;
use C0DE8\MatchMaker\Matchmaker;

/**
 * Class MatchMakerTest
 */
class MatchMakerTest extends TestCase
{

    protected $_instance;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_instance = new Matchmaker;
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * TEST
     */
    public function testFoo()
    {

    }
}
