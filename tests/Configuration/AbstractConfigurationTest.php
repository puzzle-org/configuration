<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use PHPUnit\Framework\TestCase;

class AbstractConfigurationTest extends TestCase
{
    public function testJoin(): void
    {
        $this->assertSame('a', AbstractConfiguration::join('a'));
        $this->assertSame('a/b', AbstractConfiguration::join('', 'a', '', 'b', ''));
        $this->assertSame('a/b', AbstractConfiguration::join('a', 'b'));
        $this->assertSame('a/b/c', AbstractConfiguration::join('a', 'b', 'c'));
        $this->assertSame('a/b/c/d', AbstractConfiguration::join('a', 'b', 'c', 'd'));

        $this->assertSame('foo/bar/baz/test', AbstractConfiguration::join('foo', 'bar', 'baz', 'test'));
    }
}
