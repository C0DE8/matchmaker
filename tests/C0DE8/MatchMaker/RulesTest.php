<?php

namespace C0DE8\MatchMaker\Test;

use PHPUnit\Framework\TestCase;
use C0DE8\MatchMaker\Rules;

class RulesTest extends TestCase
{

    /**
     * @var Rules
     */
    protected $_instance;


    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_instance = new Rules;
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->_instance = null;
    }


    public function testGetAllRules()
    {
        $this->assertInternalType('array', $this->_instance->get());
        $this->assertNotEmpty($this->_instance->get());
    }

    public function testGetRule()
    {
        $this->assertTrue(is_callable($this->_instance->get('any')));
    }

    public function testRules()
    {
        $this->assertTrue($this->_instance->get('nonempty')('foo'));
        $this->assertTrue($this->_instance->get('required')('foo'));
        $this->assertTrue($this->_instance->get('mixed')('foo'));
        $this->assertTrue($this->_instance->get('in')('foo', 'bar', 'foo'));
        $this->assertTrue($this->_instance->get('gt')(5, 2));
        $this->assertTrue($this->_instance->get('gte')(3, 3));
        $this->assertTrue($this->_instance->get('gte')(4, 3));
        $this->assertTrue($this->_instance->get('lt')(2, 3));
        $this->assertTrue($this->_instance->get('lte')(2, 3));
        $this->assertTrue($this->_instance->get('lte')(3, 3));
        $this->assertTrue($this->_instance->get('negative')(-1));
        $this->assertTrue($this->_instance->get('positive')(1));
        $this->assertTrue($this->_instance->get('regexp')('foo4711', '%foo4711%i'));
        $this->assertTrue($this->_instance->get('regexp')('foo4711', '%^.+?\d{4}%i'));
        $this->assertTrue($this->_instance->get('email')('test@example.com'));
        $this->assertTrue($this->_instance->get('url')('https://www.example.com'));
        $this->assertTrue($this->_instance->get('ip')('123.123.123.123'));
        $this->assertTrue($this->_instance->get('min')('x', 1));
        $this->assertTrue($this->_instance->get('max')('abcd', 4));
        $this->assertTrue($this->_instance->get('starts')('TestString', 'Test'));
        $this->assertTrue($this->_instance->get('ends')('TestString', 'String'));
        $this->assertTrue($this->_instance->get('json')('{"foo":123,"bar":[1,2,3]}'));
        $this->assertTrue($this->_instance->get('date')((new \Datetime())->format('d.m.Y')));
        $this->assertTrue($this->_instance->get('date')((new \Datetime())->format('d.m.Y H:i:s')));
        $this->assertTrue($this->_instance->get('count')([1, 2, 3], 3));
        $this->assertTrue($this->_instance->get('keys')(['foo' => 1, 'bar' => 2], 'foo', 'bar'));
        $this->assertTrue($this->_instance->get('instance')(new \stdClass(), \stdClass::class));

        $instance = new \stdClass();
        $instance->testProperty = 123;

        $this->assertTrue($this->_instance->get('property')($instance, 'testProperty', 123 ));

        $this->assertTrue($this->_instance->get('method')(new class {
            public function get()
            {
                return 42;
            }
        }, 'get', 42));

    }

    public function testRulesNegative()
    {
        $this->assertFalse($this->_instance->get('nonempty')(''));
        $this->assertFalse($this->_instance->get('required')(''));
        $this->assertFalse($this->_instance->get('in')('baz', 'bar', 'foo'));
        $this->assertFalse($this->_instance->get('gt')(1, 2));
        $this->assertFalse($this->_instance->get('gte')(2, 3));
        $this->assertFalse($this->_instance->get('gte')(1, 3));
        $this->assertFalse($this->_instance->get('lt')(3, 3));
        $this->assertFalse($this->_instance->get('lte')(4, 3));
        $this->assertFalse($this->_instance->get('negative')(1));
        $this->assertFalse($this->_instance->get('positive')(-1));
        $this->assertFalse($this->_instance->get('regexp')('foo4711', '%fooX4711%i'));
        $this->assertFalse($this->_instance->get('regexp')('foo4711', '%^.+?\d{5}%i'));
        $this->assertFalse($this->_instance->get('email')('testexample.com'));
        $this->assertFalse($this->_instance->get('url')('https:/examplecom'));
        $this->assertFalse($this->_instance->get('ip')('123.123.123.123x'));
        $this->assertFalse($this->_instance->get('ip')('123.123.256.123x'));
        $this->assertFalse($this->_instance->get('min')('ab', 3));
        $this->assertFalse($this->_instance->get('max')('abcde', 4));
        $this->assertFalse($this->_instance->get('starts')('TestString', 'XTest'));
        $this->assertFalse($this->_instance->get('ends')('TestString', 'StringX'));
        $this->assertFalse($this->_instance->get('json')('"foo":123;bar":[1,2,3]}'));
        $this->assertFalse($this->_instance->get('date')((new \Datetime())->format('d.m.Y xxx')));
        $this->assertFalse($this->_instance->get('date')((new \Datetime())->format('d.m.Y H:i:sxxx')));
        $this->assertFalse($this->_instance->get('count')([1, 2, 3], 4));
        $this->assertFalse($this->_instance->get('keys')('x', 'foo', 'bar'));
        $this->assertFalse($this->_instance->get('keys')(['foo' => 1], 'foo', 'bar'));
        $this->assertFalse($this->_instance->get('instance')('foo', \stdClass::class));
        $this->assertFalse($this->_instance->get('property')(new \stdClass(), 'testProperty', 123 ));
        $this->assertFalse($this->_instance->get('method')(new class {}, 'get', 42));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRuleException()
    {
        $this->_instance->get('not_found');
    }

    public function testAddNewRules()
    {
        $rules = $this->_instance->get(['number_five' => 5]);

        $this->assertInternalType('array', $rules);
        $this->assertArrayHasKey('number_five', $rules);
        $this->assertSame($rules, $this->_instance->get());
    }

}
