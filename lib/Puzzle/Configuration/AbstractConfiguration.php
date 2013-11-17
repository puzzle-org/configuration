<?php

namespace Puzzle\Configuration;

abstract class AbstractConfiguration implements \Puzzle\Configuration
{
    abstract public function exists($fqn);
    abstract protected function getValue($fqn);

    public function __construct()
    {
        // Empty constructor to avoid inheritance issues
    }
    
    public function read($fqn, $defaultValue = null)
    {
        $value = $defaultValue;
        
        if($this->exists($fqn))
        {
            $value = $this->getValue($fqn);
        }
    
        return $value;
    }
    
    public function readRequired($fqn)
    {
        if(!$this->exists($fqn))
        {
            throw new NotFoundException($fqn);
        }
    
        $value = $this->getValue($fqn);
    
        return $value;
    }
    
    /**
     * Parse the idenfication name of variable or group
     *
     * @example myConfigFilenameWithoutExtension/myRootConfig/myGroup/myVariable
     *
     * @param string $fqn
     * @param int $maxToken
     *
     * @throws \Puzzle\Configuration\Exceptions\InvalidIdentifier
     *
     * @return multitype:
     */
    protected function parseDsn($fqn)
    {
        return explode(self::SEPARATOR, $fqn);
    }
    
    /**
     *
     *
     * @param
     *
     * @return string
     */
    public static function join(/* ... */)
    {
        $args = func_get_args();
    
        return implode(self::SEPARATOR, $args);
    }
}