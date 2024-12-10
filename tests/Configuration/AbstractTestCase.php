<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Puzzle\Assert\ArrayRelated;
use Puzzle\Configuration;
use Puzzle\Configuration\Exceptions\NotFound;

abstract class AbstractTestCase extends TestCase
{
    use ArrayRelated;

    private const string
        DEFAULT_VALUE = 'default';

    protected Configuration
        $config;

    abstract protected function setUpConfigurationObject(): Configuration;

    protected function setUp(): void
    {
        $this->config = $this->setUpConfigurationObject();
    }

     #[DataProvider('providerTestRead')]
    public function testRead(string $fqn, string $expected): void
    {
        $value = $this->config->read($fqn, self::DEFAULT_VALUE);

        self::assertSame($expected, $value);
    }

    public static function providerTestRead(): array
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

     #[DataProvider('providerTestReadArray')]
    public function testReadArray(string $fqn, array $expected): void
    {
        $value = $this->config->readRequired($fqn);

        $this->assertSameArrayExceptOrder($expected, $value);
    }

    public static function providerTestReadArray(): array
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

    #[DataProvider('providerTestReadWithoutDefaultValue')]
    public function testReadWithoutDefaultValue(string $fqn, ?string $expected): void
    {
        $value = $this->config->read($fqn);

        self::assertSame($expected, $value);
    }

    public static function providerTestReadWithoutDefaultValue(): array
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

    #[DataProvider('providerTestReadRequired')]
    public function testReadRequired(string $fqn, string $expected): void
    {
        $value = $this->config->readRequired($fqn);

        self::assertSame($expected, $value);
    }

    public static function providerTestReadRequired(): array
    {
        return [
            ['a/b/c', 'abc'],
            ['a/b/d', 'abd'],
            ['b/b/c', 'bbc'],
            ['d/e/f', 'def'],
        ];
    }

    #[DataProvider('providerTestReadRequiredWithInvalidFQN')]
    public function testReadRequiredWithInvalidFQN(string $fqn): void
    {
        $this->expectException(NotFound::class);
        $this->config->readRequired($fqn);
    }

    public static function providerTestReadRequiredWithInvalidFQN(): array
    {
        return [
            ['a/bb/c'],
            ['g/h/i'],
            ['empty/someKey', self::DEFAULT_VALUE],
            ['commentsOnly/someKey', self::DEFAULT_VALUE],
            ['notExisting', self::DEFAULT_VALUE],
        ];
    }

    #[DataProvider('providerTestReadFirstExisting')]
    public function testReadFirstExisting($expected, array $parameters): void
    {
        $value = $this->config->readFirstExisting(...$parameters);

        self::assertSame($expected, $value);
    }

    public static function providerTestReadFirstExisting(): array
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

        self::assertSame('bbc', $value);
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
        ], iterator_to_array($this->config->all()));
    }
}
