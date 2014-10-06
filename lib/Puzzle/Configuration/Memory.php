<?php

namespace Puzzle\Configuration;

use Puzzle\Configuration;

class Memory extends AbstractConfiguration
{
    private
        $values;
    
    public function __construct(array $values)
    {
        parent::__construct();
        
        $this->values = $values;
    }
    
    public function exists($fqn)
    {
        return array_key_exists($fqn, $this->values);
    }
    
    protected function getValue($fqn)
    {
        return $this->values[$fqn];
    }
}
