<?php

class MemoryTest extends PHPUnit_Framework_TestCase
{
    const
        DEFAULT_VALUE = 'default';
    
    private
        $config;
    
    protected function setUp()
    {
        $values = array(
            'a/b/c' => 'abc', 
            'a/b/d' => 'abd', 
            'b/b/c' => 'bbc', 
            'd/e/f' => 'def', 
        );
        
        $this->config = new Puzzle\Configuration\Memory($values);
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
        );
    }
}