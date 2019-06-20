<?php

declare(strict_types = 1);

namespace Puzzle;

interface Configuration
{
    public const
        SEPARATOR = '/';

    /**
     * Return id of configuration (useful when dumping FQN from Stacked Configuration)
     *
     * @return string
     */
    public function id(): ?string;

    /**
     * Read value from configuration. Return default value if not found.
     *
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public function read(string $fqn, $defaultValue = null);

    /**
     * Read value from configuration. Throw exception if not found.
     *
     * @return mixed
     *
     * @throws \Puzzle\Configuration\Exceptions\NotFound
     */
    public function readRequired(string $fqn);

    /**
     * Read value for the first existing given key. Throw exception if not found
     *
     * @param string $fqn
     * @param ...
     * @param string $fqn
     *
     * @return mixed
     *
     * @throws \Puzzle\Configuration\Exceptions\NotFound
     */
    public function readFirstExisting(string ...$fqns);

    /**
     * Check value existence.
     */
    public function exists(string $fqn): bool;

    /**
     * List all values
     */
    public function all(): iterable;
}
