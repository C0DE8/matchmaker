<?php

namespace C0DE8\MatchMaker\Test;

use PHPUnit\Framework\TestCase;
use C0DE8\MatchMaker\KeyMatcher;

/**
 * Class KeyMatcherTest
 * @package C0DE8\MatchMaker\Test
 */
class KeyMatcherTest extends TestCase
{

    /**
     * @var KeyMatcher
     */
    protected $_instance;


    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_instance = new KeyMatcher;
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
     * TEST: \Closure
     */
    public function testInstance()
    {
        $this->assertInstanceOf(\Closure::class, $this->_instance->get([]));
    }

    /**
     * TEST: KeyMatcher with valid data
     */
    public function testKeyMatcherWithValidData()
    {
        $keyMatcher = $this->_instance->get([
            'number' => ':number',
            'string' => ':string',
        ]);

        $this->assertTrue($keyMatcher('number', 1));
        $this->assertTrue($keyMatcher('other', 1));
        $this->assertTrue($keyMatcher('string', 'some string'));
        $this->assertTrue($keyMatcher());
    }

    /**
     * @expectedException \C0DE8\MatchMaker\Exception\MatcherException
     */
    public function testKeyMatcherWithInvalidData()
    {
        $keyMatcher = $this->_instance->get([
            'number' => ':number',
            'string' => ':string',
        ]);

        $keyMatcher('string', 1);
        $keyMatcher();
    }

    /**
     * TEST: KeyMatcher Quantifiers (!, ?, *, {1,3], ...)
     */
    public function testKeyMatcherQuantifiers()
    {
        $keyMatcher = $this->_instance->get(['key' => ':number']);
        $this->assertFalse($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertFalse($keyMatcher());

        $keyMatcher = $this->_instance->get(['key!' => ':number']);
        $this->assertFalse($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertFalse($keyMatcher());

        $keyMatcher = $this->_instance->get(['key?' => ':number']);
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertFalse($keyMatcher());

        $keyMatcher = $this->_instance->get(['key*' => ':number']);
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());

        $keyMatcher = $this->_instance->get(['key{2}' => ':number']);
        $this->assertFalse($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertFalse($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());

        $keyMatcher = $this->_instance->get(['key{1,2}' => ':number']);
        $this->assertFalse($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertFalse($keyMatcher());

        $keyMatcher = $this->_instance->get([':string' => ':number']);
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());
        $this->assertTrue($keyMatcher('key', 1));
        $this->assertTrue($keyMatcher());
    }

    public function XtestNested()
    {
        $keyMatcher = $this->_instance->get([
            'article' => [
                'id' => ':number',
                'title' => ':string',
            ],
        ]);

        $this->assertFalse($keyMatcher());
        $this->assertFalse($keyMatcher('article', 1));
        $this->assertFalse($keyMatcher('article', []));
        $this->assertFalse($keyMatcher('article', ['id' => 1, 'title' => 1]));
        $this->assertTrue($keyMatcher('article', ['id' => 1, 'title' => 'some title']));
        $this->assertTrue($keyMatcher());
    }
}
