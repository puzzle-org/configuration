<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

class Memory extends AbstractConfiguration
{
    private
        $values;

    public function __construct(array $values, ?string $id = null)
    {
        parent::__construct($id);

        $this->values = $values;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function exists(string $fqn): bool
    {
        return array_key_exists($fqn, $this->values);
    }

    protected function getValue(string $fqn)
    {
        if(isset($this->values[$fqn]))
        {
            return $this->values[$fqn];
        }

        return null;
    }

    public function all(): iterable
    {
        return $this->values;
    }
}
