<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

abstract class AbstractConfiguration implements \Puzzle\Configuration
{
    /**
     * @return mixed
     */
    abstract protected function getValue(string $fqn);

    public function __construct()
    {
        // Empty constructor to avoid inheritance issues
    }
    
    public function read(string $fqn, $defaultValue = null)
    {
        $value = $defaultValue;
        
        if($this->exists($fqn))
        {
            $value = $this->getValue($fqn);
        }
    
        return $value;
    }
    
    public function readRequired(string $fqn)
    {
        if(!$this->exists($fqn))
        {
            throw new Exceptions\NotFound($fqn);
        }
    
        return $this->getValue($fqn);
    }
    
    public function readFirstExisting(string ...$fqns)
    {
        foreach($fqns as $fqn)
        {
            if($this->exists($fqn))
            {
                return $this->getValue($fqn);
            }
        }
        
        throw new Exceptions\NotFound(
            sprintf('[%s]', implode(', ', $fqns))
        );
    }
    
    /**
     * Parse the idenfication name of variable or group
     *
     * @example myConfigFilenameWithoutExtension/myRootConfig/myGroup/myVariable
     */
    protected function parseDsn(string $fqn): array
    {
        return explode(self::SEPARATOR, $fqn);
    }
    
    public static function join(string ...$parts): string
    {
        return implode(self::SEPARATOR, $parts);
    }
}
