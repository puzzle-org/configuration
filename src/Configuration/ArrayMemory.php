<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

class ArrayMemory extends AbstractConfiguration
{
    private array
        $values;

    public function __construct(array $values)
    {
        parent::__construct();

        $this->values = $values;
    }

    public function exists(string $fqn): bool
    {
        return $this->getValue($fqn) !== null;
    }

    protected function getValue(string $fqn): mixed
    {
        $keys = $this->parseDsn($fqn);
        $config = $this->values;

        while(! empty($keys))
        {
            $key = array_shift($keys);

            if(!isset($config[$key]))
            {
                return null;
            }

            $config = $config[$key];
        }

        return $config;
    }

    public function all(): iterable
    {
        return $this->flatten($this->values);
    }
}
