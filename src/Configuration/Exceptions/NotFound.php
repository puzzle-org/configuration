<?php

declare(strict_types = 1);

namespace Puzzle\Configuration\Exceptions;

class NotFound extends \Exception
{
    public function __construct(string $fqn)
    {
        parent::__construct(sprintf(
            'Configuration variable %s is missing',
            $fqn
        ));
    }
}
