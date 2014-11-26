<?php

namespace Puzzle\Configuration;

use Puzzle\Configuration;

class Stacked extends AbstractConfiguration
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

        throw new Exceptions\NotFound($fqn);
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
}