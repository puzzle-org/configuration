<?php

declare(strict_types = 1);

namespace Puzzle;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Puzzle\Configuration\Memory;

final class PrefixedConfigurationTest extends TestCase
{
    private const string
        DEFAULT_VALUE = 'default';

    private Configuration
        $configuration;

    protected function setUp(): void
    {
        $values = array(
            'a/b/c' => 'abc',
            'a/b/d' => 'abd',
            'b/b/c' => 'bbc',
            'd/e/f' => 'def',
        );

        $this->configuration = new Memory($values);
    }

    #[DataProvider('providerTestRead')]
    public function testRead(?string $prefix, string $key, string $expectedKey): void
    {
        $prefixed = new PrefixedConfiguration($this->configuration);

        if($prefix !== null)
        {
            $prefixed->setPrefix($prefix);
        }

        $this->assertSame(
            $this->configuration->read($expectedKey, self::DEFAULT_VALUE),
            $prefixed->read($key, self::DEFAULT_VALUE)
        );
    }

    public static function providerTestRead(): array
    {
        return [

            // tests without prefix
            [null, 'a/b/c', 'a/b/c'],
            [null, 'd/e/f', 'd/e/f'],
            [null, 'c', 'c'],

            // tests with prefix
            ['a/b', 'c', 'a/b/c'],
            ['a/b', 'd', 'a/b/d'],
            ['a/b', 'f', 'a/b/f'],
            ['a/b', 'b/c', 'a/b/b/c'],
            ['a/b', 'a/b/c', 'a/b/a/b/c'],

            ['d', 'e/f', 'd/e/f'],
            ['d', 'f', 'd/f'],
            ['d', 'b/c', 'd/b/c'],
            ['d', 'a/b/c', 'd/a/b/c'],

            // tests with prefix cleaning issues
            ['a/b/', 'c', 'a/b/c'],
            ['a/b/', '/c', 'a/b/c'],
            ['a/b', '/c', 'a/b/c'],
            ['a/b///', '///c', 'a/b/c'],
        ];
    }

    #[DataProvider('providerTestReadWithInvalidPrefix')]
    public function testReadWithInvalidPrefix(?string $prefix): void
    {
        $prefixed = new PrefixedConfiguration($this->configuration, $prefix);

        foreach(array('a/b/c', 'c') as $key)
        {
            $this->assertSame(
                $this->configuration->read($key, self::DEFAULT_VALUE),
                $prefixed->read($key, self::DEFAULT_VALUE)
            );
        }
    }

    public static function providerTestReadWithInvalidPrefix(): array
    {
        return [
            [''],
            ['/'],
            ['///'],
        ];
    }

    #[DataProvider('providerTestReadRequired')]
    public function testReadRequired(string $key, string $expectedkey): void
    {
        $prefixed = new PrefixedConfiguration($this->configuration, 'a/b');

        $this->assertSame(
            $this->configuration->readRequired($expectedkey),
            $prefixed->readRequired($key)
        );
    }

    public static function providerTestReadRequired(): array
    {
        return [
            ['c', 'a/b/c'],
            ['d', 'a/b/d'],
        ];
    }

    #[DataProvider('providerTestReadRequiredWithInvalidFQN')]
    public function testReadRequiredWithInvalidFQN(string $fqn): void
    {
        $prefixed = new PrefixedConfiguration($this->configuration, 'a/b');

        $this->expectException(\Puzzle\Configuration\Exceptions\NotFound::class);

        $prefixed->readRequired($fqn);
    }

    public static function providerTestReadRequiredWithInvalidFQN(): array
    {
        return [
            ['e'],
            ['a/b/c'],
        ];
    }

    public function testReadFirstExisting(): void
    {
        $prefixed = new PrefixedConfiguration($this->configuration, 'a/b');

        self::assertSame(
            $this->configuration->readFirstExisting('a/b/g', 'a/b/f', 'a/b/e', 'a/b/d', 'a/b/c', 'a/b/b', 'a/b/a'),
            $prefixed->readFirstExisting('g', 'f', 'e', 'd', 'c', 'b', 'a')
        );

        self::assertSame(
            $this->configuration->readFirstExisting('a/b/d', 'a/b/c', 'a/b/b', 'a/b/a'),
            $prefixed->readFirstExisting('d', 'c', 'b', 'a')
        );

        self::assertSame(
            $this->configuration->readFirstExisting('a/b/g', 'a/b/c'),
            $prefixed->readFirstExisting('g', 'c')
        );
    }

    public function testReadFirstExistingWithInvalidFqn(): void
    {
        $prefixed = new PrefixedConfiguration($this->configuration, 'a/b');

        $this->expectException(\Puzzle\Configuration\Exceptions\NotFound::class);

        $prefixed->readFirstExisting('x', 'y', 'z');
    }

    // Reuse same provider than testRead
    #[DataProvider('providerTestRead')]
    public function testExists(?string $prefix, string $key, string $expectedKey): void
    {
        $prefixed = new PrefixedConfiguration($this->configuration);

        if($prefix !== null)
        {
            $prefixed->setPrefix($prefix);
        }

        self::assertSame(
            $this->configuration->exists($expectedKey),
            $prefixed->exists($key)
        );
    }
}
