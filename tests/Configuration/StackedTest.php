<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use Puzzle\Configuration;

require_once __DIR__ . '/AbstractTestCase.php';

class StackedTest extends AbstractTestCase
{
    protected function setUpConfigurationObject(): Configuration
    {
        $values = array(
            'a/b/c' => 'abc',
            'a/b/d' => 'abd',
            'a/x/y' => 'wrong override value for axy',
            'b/b/c' => 'bbc',
            'd/e/f' => 'def',
            'locale/front' => ['wrong override value for locale/front'],
            'locale/back' => ['de', 'fr', 'it'],
        );

        $config = new Stacked();
        $config->overrideBy(new Memory($values));
        $config->overrideBy(new Memory([
            'a/x/y' => 'axy',
            'locale/front' => ['en', 'fr'],
        ]));

        return $config;
    }

    /**
     * @dataProvider providerTestReadArray
     */
    public function testReadArray(string $fqn, array $expected): void
    {
        // s/o for this impl (use Memory impl)
        $this->assertTrue(true);
    }

    /**
     * @dataProvider providerTestOverride
     */
    public function testOverride(Configuration $config, array $expectedValues): void
    {
        foreach($expectedValues as $key => $expected)
        {
            $this->assertSame($expected, $config->read($key, 'noVal'));
        }
    }

    public function providerTestOverride(): array
    {
        $cfg1 = new Memory(['a' => 'a1', 'b' => 'b1', 'c' => 'c1']);
        $cfg2 = new Memory(['a' => 'a2', 'b' => 'b2']);
        $cfg3 = new Memory(['a' => 'a3']);
        $cfg4 = new Memory(['c' => 'c4', 'd' => 'd4']);
        $cfg5 = new Memory(['b' => 'b5', 'd' => 'd5']);

        $emptyStack = new Stacked();
        $stack1     = (new Stacked())->overrideBy($cfg1);
        $stack12    = (new Stacked())->overrideBy($cfg1)->overrideBy($cfg2);
        $stack123   = (new Stacked())->overrideBy($cfg1)->overrideBy($cfg2)->overrideBy($cfg3);
        $stack1234  = (new Stacked())->overrideBy($cfg1)->overrideBy($cfg2)->overrideBy($cfg3)->overrideBy($cfg4);
        $stack21    = (new Stacked())->overrideBy($cfg2)->overrideBy($cfg1);
        $stack12345 = (new Stacked())->overrideBy($stack1234)->overrideBy($cfg5);

        return [
            'empty' => [$emptyStack, ['a' => 'noVal', 'b' => 'noVal', 'c' => 'noVal', 'd' => 'noVal']],
            '1'     => [$stack1, ['a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal']],
            '12'    => [$stack12, ['a' => 'a2', 'b' => 'b2', 'c' => 'c1', 'd' => 'noVal']],
            '123'   => [$stack123, ['a' => 'a3', 'b' => 'b2', 'c' => 'c1', 'd' => 'noVal']],
            '1234'  => [$stack1234, ['a' => 'a3', 'b' => 'b2', 'c' => 'c4', 'd' => 'd4']],
            '21'    => [$stack21, ['a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal']],
            '12345' => [$stack12345, ['a' => 'a3', 'b' => 'b5', 'c' => 'c4', 'd' => 'd5']],
        ];
    }

    /**
     * @dataProvider providerTestAddBase
     */
     public function testAddBase(string $configKey, array $expectedValues): void
    {
        $cfg1 = new Memory(['a' => 'a1', 'b' => 'b1', 'c' => 'c1']);
        $cfg2 = new Memory(['a' => 'a2', 'b' => 'b2', 'x' => 'x2']);
        $cfg3 = new Memory(['a' => 'a3', 'x' => 'x3', 'y' => 'y3']);
        $cfg4 = new Memory(['c' => 'c4', 'd' => 'd4', 'y' => 'y4']);
        $cfg5 = new Memory(['b' => 'b5', 'd' => 'd5']);

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

    public function providerTestAddBase(): array
    {
        return [
            'empty' => ['empty', ['a' => 'noVal', 'b' => 'noVal', 'c' => 'noVal', 'd' => 'noVal']],
            '1'     => ['1', ['a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal']],
            '12'    => ['12', ['a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal']],
            '123'   => ['123', ['a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'noVal', 'x' => 'x2']],
            '1234'  => ['1234', ['a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'd4', 'x' => 'x2', 'y' => 'y3']],
            '21'    => ['21', ['a' => 'a2', 'b' => 'b2', 'c' => 'c1', 'd' => 'noVal']],
            '12345' => ['12345', ['a' => 'a1', 'b' => 'b1', 'c' => 'c1', 'd' => 'd4', 'x' => 'x2', 'y' => 'y3']],
        ];
    }

    /**
     * @dataProvider providerTestReadFirstExistingWithStack
     */
    public function testReadFirstExistingWithStack(Configuration $config, string $expected): void
    {
        $this->assertSame($expected, $config->readFirstExisting('e', 'd', 'c', 'b', 'a'));
    }

    public function providerTestReadFirstExistingWithStack(): array
    {
        $cfg1 = new Memory(['a' => 'a1']);
        $cfg4 = new Memory(['a' => 'a4']);

        $cfg2 = new Memory(['a' => 'a2', 'b' => 'b2']);
        $cfg3 = new Memory(['a' => 'a3', 'b' => 'b3', 'c' => 'c3']);

        return [
            '1' => [(new Stacked())->overrideBy($cfg1), 'a1'],
            '2' => [(new Stacked())->overrideBy($cfg2), 'b2'],
            '3' => [(new Stacked())->overrideBy($cfg3), 'c3'],
            '4' => [(new Stacked())->overrideBy($cfg4), 'a4'],

            '12' => [(new Stacked())->overrideBy($cfg1)->overrideBy($cfg2), 'b2'],
            '21' => [(new Stacked())->overrideBy($cfg2)->overrideBy($cfg1), 'b2'],
            '11' => [(new Stacked())->overrideBy($cfg1)->overrideBy($cfg1), 'a1'],
            '14' => [(new Stacked())->overrideBy($cfg1)->overrideBy($cfg4), 'a4'],
            '41' => [(new Stacked())->overrideBy($cfg4)->overrideBy($cfg1), 'a1'],

            '123' => [(new Stacked())->overrideBy($cfg1)->overrideBy($cfg2)->overrideBy($cfg3), 'c3'],
            '321' => [(new Stacked())->overrideBy($cfg3)->overrideBy($cfg2)->overrideBy($cfg1), 'c3'],
        ];
    }

    public function testGetUnknownValue(): void
    {
        $cfg1 = new Memory(['a' => 'a1']);
        $cfg2 = new Memory(['a' => 'a2', 'b' => 'b2']);
        $cfg3 = new Memory(['a' => 'a3', 'b' => 'b3', 'c' => 'c3']);

        $config = (new Stacked())
            ->overrideBy($cfg1)
            ->overrideBy($cfg2)
            ->overrideBy($cfg3);

        $this->expectException(\Puzzle\Configuration\Exceptions\NotFound::class);

        $config->readRequired('not_exist');
    }
}
