<?php

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    const
        DEFAULT_VALUE = 'default';

    private
        $config;

    abstract protected function setUpConfigurationObject();

    protected function setUp()
    {
        $this->config = $this->setUpConfigurationObject();
    }

    /**
     * @dataProvider providerTestRead
     */
    public function testRead($fqn, $expected)
    {
        $defaultValue = self::DEFAULT_VALUE;
        $value = $this->config->read($fqn, $defaultValue);

        $this->assertSame($expected, $value);
    }

    public function providerTestRead()
    {
        return array(
            array('a/b/c', 'abc'),
            array('a/b/d', 'abd'),
            array('b/b/c', 'bbc'),
            array('d/e/f', 'def'),
            array('a/bb/c', self::DEFAULT_VALUE),
            array('g/h/i', self::DEFAULT_VALUE),
            array('empty/someKey', self::DEFAULT_VALUE),
            array('commentsOnly/someKey', self::DEFAULT_VALUE),
            array('notExisting', self::DEFAULT_VALUE),
        );
    }

    /**
     * @dataProvider providerTestReadWithoutDefaultValue
     */
    public function testReadWithoutDefaultValue($fqn, $expected)
    {
        $value = $this->config->read($fqn);

        $this->assertSame($expected, $value);
    }

    public function providerTestReadWithoutDefaultValue()
    {
        return array(
            array('a/b/c', 'abc'),
            array('a/b/d', 'abd'),
            array('b/b/c', 'bbc'),
            array('d/e/f', 'def'),
            array('a/bb/c', null),
            array('g/h/i', null),
            array('empty/someKey', null),
            array('commentsOnly/someKey', null),
            array('notExisting', null),
        );
    }

    /**
     * @dataProvider providerTestReadRequired
     */
    public function testReadRequired($fqn, $expected)
    {
        $value = $this->config->readRequired($fqn);

        $this->assertSame($expected, $value);
    }

    public function providerTestReadRequired()
    {
        return array(
            array('a/b/c', 'abc'),
            array('a/b/d', 'abd'),
            array('b/b/c', 'bbc'),
            array('d/e/f', 'def'),
        );
    }

    /**
     * @dataProvider providerTestReadRequiredWithInvalidFQN
     * @expectedException Puzzle\Configuration\Exceptions\NotFound
     */
    public function testReadRequiredWithInvalidFQN($fqn)
    {
        $value = $this->config->readRequired($fqn);
    }

    public function providerTestReadRequiredWithInvalidFQN()
    {
        return array(
            array('a/bb/c'),
            array('g/h/i'),
            array('empty/someKey', self::DEFAULT_VALUE),
            array('commentsOnly/someKey', self::DEFAULT_VALUE),
            array('notExisting', self::DEFAULT_VALUE),
        );
    }

    /**
     * @dataProvider providerTestReadFirstExisting
     */
    public function testReadFirstExisting($expected, array $parameters)
    {
        $value = call_user_func_array(array($this->config, 'readFirstExisting'), $parameters);

        $this->assertSame($expected, $value);
    }

    public function providerTestReadFirstExisting()
    {
        return array(
            array('abc', array('a/b/c', 'a/b/d',)),
            array('abd', array('a/b/d', 'a/b/c',)),
            array('def', array('x/y/z', 'd/e/f',)),
            array('bbc', array('x/y/z', 'b/b/c', 'a/b/c')),
            array('abc', array('x/y/z', 'x/y/z', 'a/b/c')),
            array('abc', array('x/y/z', 'a/b/x', 'a/b/c')),
        );
    }

    public function testReadFirstExistingNominal()
    {
        $value = $this->config->readFirstExisting('x/y/z', 'x/y', 'z/yx/', 'b/b/c', 'too/late');

        $this->assertSame('bbc', $value);
    }

    /**
     * @expectedException Puzzle\Configuration\Exceptions\NotFound
     */
    public function testReadFirstExistingNotFound()
    {
        $this->config->readFirstExisting('x/y/z', 'x/y', 'z/yx/');
    }

    /**
     * @expectedException Puzzle\Configuration\Exceptions\NotFound
     */
    public function testReadFirstExistingWithoutAnyArgument()
    {
        $this->config->readFirstExisting();
    }
}
