<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use PHPUnit\Framework\TestCase;
use Puzzle\Assert\ArrayRelated;
use Puzzle\Configuration;

abstract class AbstractTestCase extends TestCase
{
    use ArrayRelated;

    private const
        DEFAULT_VALUE = 'default';

    protected
        $config;

    abstract protected function setUpConfigurationObject(): Configuration;

    protected function setUp(): void
    {
        $this->config = $this->setUpConfigurationObject();
    }

    /**
     * @dataProvider providerTestRead
     */
    public function testRead(string $fqn, string $expected): void
    {
        $value = $this->config->read($fqn, self::DEFAULT_VALUE);

        $this->assertSame($expected, $value);
    }

    public function providerTestRead(): array
    {
        return [
            ['a/b/c', 'abc'],
            ['a/b/d', 'abd'],
            ['a/x/y', 'axy'],
            ['b/b/c', 'bbc'],
            ['d/e/f', 'def'],
            ['a/bb/c', self::DEFAULT_VALUE],
            ['g/h/i', self::DEFAULT_VALUE],
            ['empty/someKey', self::DEFAULT_VALUE],
            ['commentsOnly/someKey', self::DEFAULT_VALUE],
            ['notExisting', self::DEFAULT_VALUE],
        ];
    }

    /**
     * @dataProvider providerTestReadArray
     */
    public function testReadArray(string $fqn, array $expected): void
    {
        $value = $this->config->readRequired($fqn);

        $this->assertSameArrayExceptOrder($expected, $value);
    }

    public function providerTestReadArray(): array
    {
        return [
            ['a/b', ['c' => 'abc', 'd' => 'abd']],
            ['a', [
                'b' =>
                    ['c' => 'abc', 'd' => 'abd'],
                'x' =>
                    ['y' => 'axy'],
            ]],

            ['locale/front', ['en', 'fr']],
            ['locale/back', ['de', 'fr', 'it']],
        ];
    }

    /**
     * @dataProvider providerTestReadWithoutDefaultValue
     */
    public function testReadWithoutDefaultValue(string $fqn, ?string $expected): void
    {
        $value = $this->config->read($fqn);

        $this->assertSame($expected, $value);
    }

    public function providerTestReadWithoutDefaultValue(): array
    {
        return [
            ['a/b/c', 'abc'],
            ['a/b/d', 'abd'],
            ['b/b/c', 'bbc'],
            ['d/e/f', 'def'],
            ['a/bb/c', null],
            ['g/h/i', null],
            ['empty/someKey', null],
            ['commentsOnly/someKey', null],
            ['notExisting', null],
        ];
    }

    /**
     * @dataProvider providerTestReadRequired
     */
    public function testReadRequired(string $fqn, string $expected): void
    {
        $value = $this->config->readRequired($fqn);

        $this->assertSame($expected, $value);
    }

    public function providerTestReadRequired(): array
    {
        return [
            ['a/b/c', 'abc'],
            ['a/b/d', 'abd'],
            ['b/b/c', 'bbc'],
            ['d/e/f', 'def'],
        ];
    }

    /**
     * @dataProvider providerTestReadRequiredWithInvalidFQN
     */
    public function testReadRequiredWithInvalidFQN(string $fqn): void
    {
        $this->expectException(\Puzzle\Configuration\Exceptions\NotFound::class);

        $this->config->readRequired($fqn);
    }

    public function providerTestReadRequiredWithInvalidFQN(): array
    {
        return [
            ['a/bb/c'],
            ['g/h/i'],
            ['empty/someKey', self::DEFAULT_VALUE],
            ['commentsOnly/someKey', self::DEFAULT_VALUE],
            ['notExisting', self::DEFAULT_VALUE],
        ];
    }

    /**
     * @dataProvider providerTestReadFirstExisting
     */
    public function testReadFirstExisting($expected, array $parameters): void
    {
        $value = $this->config->readFirstExisting(...$parameters);

        $this->assertSame($expected, $value);
    }

    public function providerTestReadFirstExisting(): array
    {
        return [
            ['abc', ['a/b/c', 'a/b/d',]],
            ['abd', ['a/b/d', 'a/b/c',]],
            ['def', ['x/y/z', 'd/e/f',]],
            ['bbc', ['x/y/z', 'b/b/c', 'a/b/c']],
            ['abc', ['x/y/z', 'x/y/z', 'a/b/c']],
            ['abc', ['x/y/z', 'a/b/x', 'a/b/c']],
        ];
    }

    public function testReadFirstExistingNominal(): void
    {
        $value = $this->config->readFirstExisting('x/y/z', 'x/y', 'z/yx/', 'b/b/c', 'too/late');

        $this->assertSame('bbc', $value);
    }

    public function testReadFirstExistingNotFound(): void
    {
        $this->expectException(\Puzzle\Configuration\Exceptions\NotFound::class);

        $this->config->readFirstExisting('x/y/z', 'x/y', 'z/yx/');
    }

    public function testReadFirstExistingWithoutAnyArgument(): void
    {
        $this->expectException(\Puzzle\Configuration\Exceptions\NotFound::class);

        $this->config->readFirstExisting();
    }

    public function testAll(): void
    {
        $this->assertSameArrayExceptOrder([
            'a/b/c' => 'abc',
            'a/b/d' => 'abd',
            'a/x/y' => 'axy',
            'b/b/c' => 'bbc',
            'd/e/f' => 'def',
            'locale/front' => [
                'en', 'fr'
            ],
            'locale/bacl' => [
                'de', 'fr', 'it'
            ]
        ], $this->config->all());
    }
}
