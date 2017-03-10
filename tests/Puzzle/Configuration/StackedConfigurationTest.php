<?php

namespace Puzzle\Configuration;

use Puzzle\Configuration;

require_once __DIR__ . '/AbstractTestCase.php';

class StackedConfigurationTest extends \AbstractTestCase
{
    protected function setUpConfigurationObject()
    {
        $values = array(
            'a/b/c' => 'abc',
            'a/b/d' => 'abd',
            'b/b/c' => 'bbc',
            'd/e/f' => 'def',
        );

        $config = new Stacked();
        $config->overrideBy(new Memory($values));

        return $config;
    }

    /**
     * @dataProvider providerTestOverride
     */
    public function testOverride(Configuration $config, array $expectedValues)
    {
        foreach($expectedValues as $key => $expected)
        {
            $this->assertSame($expected, $config->read($key, 'noVal'));
        }
    }

    public function providerTestOverride()
    {
        $cfg1 = new Memory(array('a' => 'a1', 'b' => 'b1', 'c' => 'c1'));
        $cfg2 = new Memory(array('a' => 'a2', 'b' => 'b2'));
        $cfg3 = new Memory(array('a' => 'a3'));
        $cfg4 = new Memory(array('c' => 'c4', 'd' => 'd4'));
        $cfg5 = new Memory(array('b' => 'b5', 'd' => 'd5'));

        $emptyStack = new Stacked();
        $stack1     = (new Stacked())->overrideBy($cfg1);
        $stack12    = (new Stacked())->overrideBy($cfg1)->overrideBy($cfg2);
        $stack123   = (new Stacked())->overrideBy($cfg1)->overrideBy($cfg2)->overrideBy($cfg3);
        $stack1234  = (new Stacked())->overrideBy($cfg1)->overrideBy($cfg2)->overrideBy($cfg3)->overrideBy($cfg4);
        $stack21    = (new Stacked())->overrideBy($cfg2)->overrideBy($cfg1);
        $stack12345 = (new Stacked())->overrideBy($stack1234)->overrideBy($cfg5);

        return array(
            'empty' => array($emptyStack, array('a' => 'noVal', 'b' => 'noVal', 'c' => 'noVal', 'd' => 'noVal')),
            '1'     => array($stack1,     array('a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal')),
            '12'    => array($stack12,    array('a' => 'a2', 'b' => 'b2', 'c' => 'c1', 'd' => 'noVal')),
            '123'   => array($stack123,   array('a' => 'a3', 'b' => 'b2', 'c' => 'c1', 'd' => 'noVal')),
            '1234'  => array($stack1234,  array('a' => 'a3', 'b' => 'b2', 'c' => 'c4', 'd' => 'd4')),
            '21'    => array($stack21,    array('a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal')),
            '12345' => array($stack12345, array('a' => 'a3', 'b' => 'b5', 'c' => 'c4', 'd' => 'd5')),
        );
    }

    /**
     * @dataProvider providerTestAddBase
     */
     public function testAddBase($configKey, array $expectedValues)
    {
        $cfg1 = new Memory(array('a' => 'a1', 'b' => 'b1', 'c' => 'c1'));
        $cfg2 = new Memory(array('a' => 'a2', 'b' => 'b2', 'x' => 'x2'));
        $cfg3 = new Memory(array('a' => 'a3', 'x' => 'x3', 'y' => 'y3'));
        $cfg4 = new Memory(array('c' => 'c4', 'd' => 'd4', 'y' => 'y4'));
        $cfg5 = new Memory(array('b' => 'b5', 'd' => 'd5'));

        $stacks = [
            'empty' => new Stacked(),
            '1'     => (new Stacked())->addBase($cfg1),
            '12'    => (new Stacked())->addBase($cfg1)->addBase($cfg2),
            '123'   => (new Stacked())->addBase($cfg1)->addBase($cfg2)->addBase($cfg3),
            '1234'  => (new Stacked())->addBase($cfg1)->addBase($cfg2)->addBase($cfg3)->addBase($cfg4),
            '21'    => (new Stacked())->addBase($cfg2)->addBase($cfg1),
        ];
        $stacks['12345'] = (new Stacked())->addBase($stacks['1234'])->addBase($cfg5);

        $config = $stacks[$configKey];

        foreach($expectedValues as $key => $expected)
        {
            $this->assertSame($expected, $config->read($key, 'noVal'));
        }
    }

    public function providerTestAddBase()
    {

        return array(
            'empty' => array('empty', array('a' => 'noVal', 'b' => 'noVal', 'c' => 'noVal', 'd' => 'noVal')),
            '1'     => array('1',     array('a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal')),
            '12'    => array('12',    array('a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal')),
            '123'   => array('123',   array('a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal', 'x' => 'x2')),
            '1234'  => array('1234',  array('a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'd4', 'x' => 'x2', 'y' => 'y3')),
            '21'    => array('21',    array('a' => 'a2', 'b' => 'b2', 'c' => 'c1', 'd' => 'noVal')),
            '12345' => array('12345', array('a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'd4', 'x' => 'x2', 'y' => 'y3')),
        );
    }

    /**
     * @dataProvider providerTestReadFirstExistingWithStack
     */
    public function testReadFirstExistingWithStack(Configuration $config, $expected)
    {
        $this->assertSame($expected, $config->readFirstExisting('e', 'd', 'c', 'b', 'a'));
    }

    public function providerTestReadFirstExistingWithStack()
    {
        $cfg1 = new Memory(array('a' => 'a1'));
        $cfg4 = new Memory(array('a' => 'a4'));

        $cfg2 = new Memory(array('a' => 'a2', 'b' => 'b2'));
        $cfg3 = new Memory(array('a' => 'a3', 'b' => 'b3', 'c' => 'c3'));

        return array(
            '1' => array((new Stacked())->overrideBy($cfg1), 'a1'),
            '2' => array((new Stacked())->overrideBy($cfg2), 'b2'),
            '3' => array((new Stacked())->overrideBy($cfg3), 'c3'),
            '4' => array((new Stacked())->overrideBy($cfg4), 'a4'),

            '12' => array((new Stacked())->overrideBy($cfg1)->overrideBy($cfg2), 'b2'),
            '21' => array((new Stacked())->overrideBy($cfg2)->overrideBy($cfg1), 'b2'),
            '11' => array((new Stacked())->overrideBy($cfg1)->overrideBy($cfg1), 'a1'),
            '14' => array((new Stacked())->overrideBy($cfg1)->overrideBy($cfg4), 'a4'),
            '41' => array((new Stacked())->overrideBy($cfg4)->overrideBy($cfg1), 'a1'),

            '123' => array((new Stacked())->overrideBy($cfg1)->overrideBy($cfg2)->overrideBy($cfg3), 'c3'),
            '321' => array((new Stacked())->overrideBy($cfg3)->overrideBy($cfg2)->overrideBy($cfg1), 'c3'),
        );
    }

    /**
     * @expectedException \Puzzle\Configuration\Exceptions\NotFound
     */
    public function testGetUnknownValue()
    {
        $cfg1 = new Memory(array('a' => 'a1'));
        $cfg2 = new Memory(array('a' => 'a2', 'b' => 'b2'));
        $cfg3 = new Memory(array('a' => 'a3', 'b' => 'b3', 'c' => 'c3'));

        $config = (new Stacked())
            ->overrideBy($cfg1)
            ->overrideBy($cfg2)
            ->overrideBy($cfg3);

        $config->readRequired('not_exist');
    }
}