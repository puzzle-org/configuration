<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use Puzzle\Configuration;
use Puzzle\ConfigurationSystem;

class Stacked extends AbstractConfiguration implements ConfigurationSystem
{
    private
        $stack;

    public function __construct(?string $id = null)
    {
        parent::__construct($id);

        $this->stack = new \SplStack();
    }

    protected function getValue(string $fqn)
    {
        foreach($this->stack as $config)
        {
            if($config->exists($fqn))
            {
                return $config->getValue($fqn);
            }
        }

        return null;
    }

    public function exists(string $fqn): bool
    {
        foreach($this->stack as $config)
        {
            if($config->exists($fqn))
            {
                return true;
            }
        }

        return false;
    }

    public function all(): iterable
    {
        $all = [];

        foreach($this->stack as $config)
        {
            $all += $config->all();
        }

        return $all;
    }

    public function overrideBy(Configuration $configuration): ConfigurationSystem
    {
        $this->stack->push($configuration);

        return $this;
    }

    public function addBase(Configuration $configuration): ConfigurationSystem
    {
        $this->stack->unshift($configuration);

        return $this;
    }

    public function dump(string $fqn): iterable
    {
        $result = [];

        foreach($this->stack as $index => $config)
        {
            $value = $config->read($fqn);

            if ($value !== null)
            {
                $result[$config->id() ?? $index] = $config->read($fqn);
            }
        }

        return $result;
    }
}
