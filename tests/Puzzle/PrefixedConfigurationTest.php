<?php

namespace Puzzle;

use Puzzle\Configuration\Memory;

class PrefixedConfigurationTest extends \PHPUnit_Framework_TestCase
{
    const
        DEFAULT_VALUE = 'default';

    private
        $configuration;

    protected function setUp()
    {
        $values = array(
            'a/b/c' => 'abc',
            'a/b/d' => 'abd',
            'b/b/c' => 'bbc',
            'd/e/f' => 'def',
        );

        $this->configuration = new Memory($values);
    }

    /**
     * @dataProvider providerTestRead
     */
    public function testRead($prefix, $key, $expectedKey)
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

    public function providerTestRead()
    {
        return array(

            // tests without prefix
            array(null, 'a/b/c', 'a/b/c'),
            array(null, 'd/e/f', 'd/e/f'),
            array(null, 'c', 'c'),

            // tests with prefix
            array('a/b', 'c', 'a/b/c'),
            array('a/b', 'd', 'a/b/d'),
            array('a/b', 'f', 'a/b/f'),
            array('a/b', 'b/c', 'a/b/b/c'),
            array('a/b', 'a/b/c', 'a/b/a/b/c'),

            array('d', 'e/f', 'd/e/f'),
            array('d', 'f', 'd/f'),
            array('d', 'b/c', 'd/b/c'),
            array('d', 'a/b/c', 'd/a/b/c'),

            // tests with prefix cleaning issues
            array('a/b/', 'c', 'a/b/c'),
            array('a/b/', '/c', 'a/b/c'),
            array('a/b', '/c', 'a/b/c'),
            array('a/b///', '///c', 'a/b/c'),
        );
    }

    /**
     * @dataProvider providerTestReadWithInvalidPrefix
     */
    public function testReadWithInvalidPrefix($prefix)
    {
        $prefixed = new PrefixedConfiguration($this->configuration);
        $prefixed->setPrefix($prefix);

        foreach(array('a/b/c', 'c') as $key)
        {
            $this->assertSame(
                $this->configuration->read($key, self::DEFAULT_VALUE),
                $prefixed->read($key, self::DEFAULT_VALUE)
            );
        }
    }

    public function providerTestReadWithInvalidPrefix()
    {
        return array(
            array(false),
            array(true),
            array(0),
            array(42),
            array(''),
            array('/'),
            array('///'),
            array(array()),
            array(array('pony')),
            array(function() {}),
            array(new \stdClass()),
        );
    }

    /**
     * @dataProvider providerTestReadRequired
     */
    public function testReadRequired($key, $expectedkey)
    {
        $prefixed = new PrefixedConfiguration($this->configuration);
        $prefixed->setPrefix('a/b');

        $this->assertSame(
            $this->configuration->readRequired($expectedkey),
            $prefixed->readRequired($key)
        );
    }

    public function providerTestReadRequired()
    {
        return array(
            array('c', 'a/b/c'),
            array('d', 'a/b/d'),
        );
    }

    /**
     * @dataProvider providerTestReadRequiredWithInvalidFQN
     * @expectedException Puzzle\Configuration\Exceptions\NotFound
     */
    public function testReadRequiredWithInvalidFQN($fqn)
    {
        $prefixed = new PrefixedConfiguration($this->configuration);
        $prefixed->setPrefix('a/b');

        $prefixed->readRequired($fqn);
    }

    public function providerTestReadRequiredWithInvalidFQN()
    {
        return array(
            array('e'),
            array('a/b/c'),
        );
    }

    public function testReadFirstExisting()
    {
        $prefixed = new PrefixedConfiguration($this->configuration);
        $prefixed->setPrefix('a/b');

        $this->assertSame(
            $this->configuration->readFirstExisting('a/b/g', 'a/b/f', 'a/b/e', 'a/b/d', 'a/b/c', 'a/b/b', 'a/b/a'),
            $prefixed->readFirstExisting('g', 'f', 'e', 'd', 'c', 'b', 'a')
        );

        $this->assertSame(
            $this->configuration->readFirstExisting('a/b/d', 'a/b/c', 'a/b/b', 'a/b/a'),
            $prefixed->readFirstExisting('d', 'c', 'b', 'a')
        );

        $this->assertSame(
            $this->configuration->readFirstExisting('a/b/g', 'a/b/c'),
            $prefixed->readFirstExisting('g', 'c')
        );
    }

    /**
     * @expectedException \Puzzle\Configuration\Exceptions\NotFound
     */
    public function testReadFirstExistingWithInvalidFqn()
    {
        $prefixed = new PrefixedConfiguration($this->configuration);
        $prefixed->setPrefix('a/b');

        $prefixed->readFirstExisting('x', 'y', 'z');
    }

    /**
     * Reuse same provider than testRead
     * @dataProvider providerTestRead
     */
    public function testExists($prefix, $key, $expectedKey)
    {
        $prefixed = new PrefixedConfiguration($this->configuration);

        if($prefix !== null)
        {
            $prefixed->setPrefix($prefix);
        }

        $this->assertSame(
            $this->configuration->exists($expectedKey),
            $prefixed->exists($key)
        );
    }
}
