<?php

declare(strict_types = 1);

namespace Puzzle\Configuration\FS\Adapters;

use Puzzle\Configuration\FS\Filesystem;
use \Puzzle\Configuration\Exceptions\FileNotFound;

final readonly class GaufretteAdapter implements Filesystem
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
            // @ mandatory because of Gaufrette lack of maintenance
            // Without @, it triggers PHP deprecation because of implicit nullable argument while throwing FileNotFound exception
            return @$this->fs->read($key);
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

