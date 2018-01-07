<?php

namespace C0DE8\MatchMaker\Test;

use PHPUnit\Framework\TestCase;
use C0DE8\MatchMaker\Matcher;

/**
 * Class MatcherTest
 * @package C0DE8\MatchMaker\Test
 */
class MatcherTest extends TestCase
{

    /**
     * @var Matcher
     */
    protected $_instance;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_instance = new Matcher;
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_instance = null;
    }


    /**
     * test constant values
     */
    public function testMatchConstant()
    {
        $this->assertTrue($this->_instance->match(1, 1));
        $this->assertTrue($this->_instance->match('string', 'string'));
        $this->assertTrue($this->_instance->match(true, true));
        $this->assertFalse($this->_instance->match(1, 2));
        $this->assertFalse($this->_instance->match('string', 'other_string'));
        $this->assertFalse($this->_instance->match('string', 'other_string'));
    }

    /**
     * test normal values
     */
    public function testMatcher()
    {
        $this->assertTrue($this->_instance->match(1, ':integer'));
        $this->assertFalse($this->_instance->match('not_integer', ':integer'));
    }

    /**
     * test multiple
     */
    public function testMatcherMulti()
    {
        $this->assertTrue($this->_instance->match('1', ':string number'));
        $this->assertFalse($this->_instance->match('string', ':string number'));
    }

    /**
     * test with arguments
     */
    public function testMatcherWithArgs()
    {
        $this->assertTrue($this->_instance->match(6, ':integer gt(5)'));
        $this->assertFalse($this->_instance->match(4, ':integer gt(5)'));
        $this->assertTrue($this->_instance->match(4, ':integer between(1,5)'));
        $this->assertFalse($this->_instance->match(7, ':integer between(1,5)'));
    }

    /**
     * test an empty string
     */
    public function testMatchEmptyString()
    {
        $this->assertTrue($this->_instance->match('any_value', ''));
    }

}
