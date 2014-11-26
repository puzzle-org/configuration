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
}