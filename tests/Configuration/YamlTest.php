<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use Puzzle\Configuration;
use Puzzle\Configuration\Gaufrette\Memory;
use Puzzle\Configuration\FS\Adapters\GaufretteAdapter;
use Gaufrette\Filesystem;

// Patch Gaufrette to avoid PHP Deprecations (explicit nullable argument with php 8.4)
require_once __DIR__ . '/Gaufrette/Memory.php';

class YamlTest extends AbstractTestCase
{
    protected function setUpConfigurationObject(): Configuration
    {
        $adapter = new Memory();

        $content = <<<YAML
b:
  c: abc
  d: abd
x:
  y: axy
YAML;
        $adapter->write('a.yml', $content);

        $content = <<<YAML
b:
  c: bbc
YAML;
        $adapter->write('b.yml', $content);

        $content = <<<YAML
b:
  c: fakeValue
YAML;
        $adapter->write('b.yml-fake', $content);

        $content = <<<YAML
e:
  f: def
YAML;
        $adapter->write('d.yml', $content);

        $content = '';
        $adapter->write('empty.yml', $content);

        $content = <<<YAML
# This is a file with only comments in it
# Like this one
# Right here
# Did you see ?

YAML;
        $adapter->write('commentsOnly.yml', $content);

        $content = <<<YAML
front:
    - en
    - fr
back:
    - de
    - fr
    - it
YAML;
        $adapter->write('locale.yml', $content);

        return new Yaml(
            new GaufretteAdapter(
                new Filesystem($adapter)
        ));
    }
}
