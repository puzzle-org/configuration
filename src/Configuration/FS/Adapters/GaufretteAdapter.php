<?php

declare(strict_types = 1);

namespace Puzzle\Configuration\FS\Adapters;

use Puzzle\Configuration\FS\Filesystem;
use \Puzzle\Configuration\Exceptions\FileNotFound;

final class GaufretteAdapter implements Filesystem
{
    private \Gaufrette\Filesystem
        $fs;

    public function __construct(\Gaufrette\Filesystem $fs)
    {
        $this->fs = $fs;
    }

    public function read(string $key): string
    {
        try
        {
            return $this->fs->read($key);
        }
        catch(\Gaufrette\Exception\FileNotFound)
        {
            throw new FileNotFound();
        }
    }

    public function keys(): iterable
    {
        return $this->fs->keys();
    }
}

