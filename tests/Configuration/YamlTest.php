<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use Gaufrette\Filesystem;
use Gaufrette\Adapter\InMemory;
use Puzzle\Configuration;

class YamlTest extends AbstractTestCase
{
    protected function setUpConfigurationObject(): Configuration
    {
        $adapter = new InMemory();

        $content = <<<YAML
b:
  c: abc
  d: abd
YAML;
        $adapter->write('a.yml', $content);

        $content = <<<YAML
b:
  c: bbc
YAML;
        $adapter->write('b.yml', $content);

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

        return new Yaml(new Filesystem($adapter));
    }
}
