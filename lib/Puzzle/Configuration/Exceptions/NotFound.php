<?php

namespace Puzzle\Configuration\Exceptions;

class NotFound extends \Exception
{
    public function __construct($fqn)
    {
        parent::__construct(sprintf(
            'Configuration variable %s is missing',
            $fqn
        ));
    }
}