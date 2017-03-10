<?php

namespace Puzzle\Configuration;

use Puzzle\Configuration;
use Puzzle\ConfigurationSystem;

class Stacked extends AbstractConfiguration implements ConfigurationSystem
{
    private
        $stack;

    public function __construct()
    {
        parent::__construct();

        $this->stack = new \SplStack();
    }

    protected function getValue($fqn)
    {
        foreach($this->stack as $config)
        {
            if($config->exists($fqn))
            {
                return $config->getValue($fqn);
            }
        }
    }

    public function exists($fqn)
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

    public function overrideBy(Configuration $configuration)
    {
        $this->stack->push($configuration);

        return $this;
    }

    public function addBase(Configuration $configuration)
    {
        $this->stack->unshift($configuration);

        return $this;
    }
}
