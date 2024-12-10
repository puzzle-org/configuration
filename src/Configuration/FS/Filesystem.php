<?php

namespace Puzzle\Configuration\FS;

interface Filesystem
{
    /**
     * @throws \Puzzle\Configuration\Exceptions\FileNotFound
     */
    public function read(string $key): string;

    public function keys(): iterable;
}
