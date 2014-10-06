<?php

use Puzzle\Configuration\AbstractConfiguration;

class AbstractConfigurationTest extends PHPUnit_Framework_TestCase
{
    public function testJoin()
    {
        $this->assertSame('a', AbstractConfiguration::join('a'));
        $this->assertSame('a/b', AbstractConfiguration::join('a', 'b'));
        $this->assertSame('a/b/c', AbstractConfiguration::join('a', 'b', 'c'));
        $this->assertSame('a/b/c/d', AbstractConfiguration::join('a', 'b', 'c', 'd'));
        
        $this->assertSame('foo/bar/baz/test', AbstractConfiguration::join('foo', 'bar', 'baz', 'test'));
    }
}