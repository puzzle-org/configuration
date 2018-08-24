<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use PHPUnit\Framework\TestCase;
use Puzzle\Configuration;

abstract class AbstractTestCase extends TestCase
{
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
        $defaultValue = self::DEFAULT_VALUE;
        $value = $this->config->read($fqn, $defaultValue);

        $this->assertSame($expected, $value);
    }

    public function providerTestRead(): array
    {
        return [
            ['a/b/c', 'abc'],
            ['a/b/d', 'abd'],
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
     * @expectedException \Puzzle\Configuration\Exceptions\NotFound
     */
    public function testReadRequiredWithInvalidFQN(string $fqn): void
    {
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

    /**
     * @expectedException \Puzzle\Configuration\Exceptions\NotFound
     */
    public function testReadFirstExistingNotFound(): void
    {
        $this->config->readFirstExisting('x/y/z', 'x/y', 'z/yx/');
    }

    /**
     * @expectedException \Puzzle\Configuration\Exceptions\NotFound
     */
    public function testReadFirstExistingWithoutAnyArgument(): void
    {
        $this->config->readFirstExisting();
    }
}
