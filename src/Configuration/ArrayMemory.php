<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

class ArrayMemory extends AbstractConfiguration
{
    private
        $values;

    public function __construct(array $values, ?string $id = null)
    {
        parent::__construct($id);

        $this->values = $values;
    }

    public function exists(string $fqn): bool
    {
        return $this->getValue($fqn) !== null;
    }

    protected function getValue(string $fqn)
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
