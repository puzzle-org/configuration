<?php

namespace Puzzle\Configuration\Exceptions;

class InvalidIdentifier extends \Exception
{
    public function __construct($id)
    {
        parent::__construct(sprintf(
            '%s is not a valid configuration identifier',
            $id
        ));
    }
}