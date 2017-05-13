<?php

namespace C0DE8\MatchMaker\Test;

use PHPUnit\Framework\TestCase;
use C0DE8\MatchMaker\Manager;

/**
 * Class ManagerTest
 * @package C0DE8\MatchMaker\Test
 */
class ManagerTest extends TestCase
{

    /**
     * @var Manager
     */
    protected $_instance;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->_instance = new Manager;
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
     * @expectedException \C0DE8\MatchMaker\Exception\InvalidValueTypeException
     */
    public function testInvalidArray()
    {
        $this->_instance->matchAgainst(1, [1]);
    }

    /**
     * @expectedException \C0DE8\MatchMaker\Exception\MatcherException
     */
    public function testScalar()
    {
        $this->assertTrue($this->_instance->matchAgainst(1, ':integer'));
        $this->assertFalse($this->_instance->matchAgainst('string', ':integer'));
    }

    /**
     * @expectedException \C0DE8\MatchMaker\Exception\KeyMatcherFailException
     */
    public function testWrongCountRaisesKeyMatcherException()
    {
        $this->_instance->matchAgainst(
            [
                'foo' => 123
            ],
            [
                ':string {2,3}' => ':int'
            ]
        );
    }

    public function testAutoCountFromZeroToPhpMaxInt()
    {
        $this->assertTrue(
            $this->_instance->matchAgainst(
                [
                    'foo' => 123,
                    'bar' => 456,
                    'baz' => 789
                ],
                [
                    ':string {,}' => ':int'
                ]
            )
        );
    }

    /**
     * @return array
     */
    public function arrayPatternDataProvider() : array
    {
        return [
            [[
                '*' => [
                    'id'    => ':integer gt(0)',
                    'title' => ':string contains(super)',
                ],
            ]]
        ];
    }

    /**
     * @dataProvider arrayPatternDataProvider
     * @param array $pattern
     */
    public function testArrayWithValidData(array $pattern)
    {
        $this->assertTrue(
            $this->_instance->matchAgainst(
                [
                    [
                        'id' => 1,
                        'title' => 'super cool book'
                    ],
                    [
                        'id' => 2,
                        'title' => 'another super cool book'
                    ],
                ],
                $pattern
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayWithInvalidArguments()
    {
        $this->_instance->matchAgainst(
            [
                [
                    'id' => 1,
                    'title' => 'super cool book'
                ],
            ],
            [
                ':foo' => ':bar'
            ]
        );
    }

    /**
     * @dataProvider arrayPatternDataProvider
     * @param array $pattern
     * @expectedException \C0DE8\MatchMaker\Exception\MatcherException
     */
    public function testArrayWithInvalidValues(array $pattern)
    {
        $this->_instance->matchAgainst(
            [
                [
                    'id' => 1,
                    'title' => 'just book'
                ],
            ],
            $pattern
        );

        $this->_instance->matchAgainst(
            null,
            $pattern
        );
    }

    /**
     *
     */
    public function testMatchAgainstWithValidExampleData()
    {
        $value = [
            [
                'type'     => 'book',
                'title'    => 'Geography book',
                'chapters' => [
                    'eu' => ['title' => 'Europe', 'interesting'  => true],
                    'as' => ['title' => 'America', 'interesting' => false]
                ],
                'price'    => 19.99
            ],
            [
                'type'     => 'book',
                'title'    => 'Foreign languages book',
                'chapters' => [
                    'de' => ['title' => 'Deutsch']
                ],
                'price'    => 29.99
            ]
        ];

        $pattern = [
            '*' => [
                'type'     => 'book',
                'title'    => ':string contains(book)',
                'chapters' => [
                    ':string length(2) {1,3}' => [
                        'title'        => ':string',
                        'interesting?' => ':bool',
                    ]
                ],
                'price'    => ':float',
                'foo {}'      => []
            ]
        ];

        $this->assertTrue($this->_instance->matchAgainst($value, $pattern));
    }

}
