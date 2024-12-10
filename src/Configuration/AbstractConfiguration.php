<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

abstract class AbstractConfiguration implements \Puzzle\Configuration
{
    abstract protected function getValue(string $fqn): mixed;

    public function __construct()
    {
        // Empty constructor to avoid inheritance issues
    }
    
    public function read(string $fqn, mixed $defaultValue = null): mixed
    {
        $value = $defaultValue;
        
        if($this->exists($fqn))
        {
            $value = $this->getValue($fqn);
        }
    
        return $value;
    }
    
    public function readRequired(string $fqn): mixed
    {
        if(!$this->exists($fqn))
        {
            throw new Exceptions\NotFound($fqn);
        }
    
        return $this->getValue($fqn);
    }
    
    public function readFirstExisting(string ...$fqns): mixed
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
        return implode(self::SEPARATOR, array_filter($parts));
    }

    protected function flatten(array $values, string $root = ''): array
    {
        $result = [];

        foreach($values as $key => $value)
        {
            $fqn = self::join($root, $key);

            if($this->isHash($value))
            {
                $result += $this->flatten($value, $fqn);

                continue;
            }

            $result[$fqn] = $value;
        }

        return $result;
    }

    private function isHash($value): bool
    {
        if(! is_array($value))
        {
            return false;
        }

        foreach(array_keys($value) as $key)
        {
            if(! is_numeric($key))
            {
                return true;
            }
        }

        return false;
    }
}
