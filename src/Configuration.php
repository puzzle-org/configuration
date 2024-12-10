<?php

declare(strict_types = 1);

namespace Puzzle;

interface Configuration
{
    public const string
        SEPARATOR = '/';
    
    /**
     * Read value from configuration. Return default value if not found.
     */
    public function read(string $fqn, mixed $defaultValue = null): mixed;
    
    /**
     * Read value from configuration. Throw exception if not found.
     *
     * @throws \Puzzle\Configuration\Exceptions\NotFound
     */
    public function readRequired(string $fqn): mixed;
    
    /**
     * Read value for the first existing given key. Throw exception if not found
     * 
     * @throws \Puzzle\Configuration\Exceptions\NotFound
     */
    public function readFirstExisting(string ...$fqns): mixed;
    
    /**
     * Check value existence.
     */
    public function exists(string $fqn): bool;

    /**
     * List all values
     */
    public function all(): iterable;
}
