<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use PHPUnit\Framework\TestCase;

class AbstractConfigurationTest extends TestCase
{
    public function testJoin(): void
    {
        self::assertSame('a/b', AbstractConfiguration::join('', 'a', '', 'b', ''));
        self::assertSame('a', AbstractConfiguration::join('a'));
        self::assertSame('a/b', AbstractConfiguration::join('a', 'b'));
        self::assertSame('a/b/c', AbstractConfiguration::join('a', 'b', 'c'));
        self::assertSame('a/b/c/d', AbstractConfiguration::join('a', 'b', 'c', 'd'));

        self::assertSame('foo/bar/baz/test', AbstractConfiguration::join('foo', 'bar', 'baz', 'test'));
    }
}
