<?php

use Gaufrette\Filesystem;
use Gaufrette\Adapter\InMemory;

class YamlTest extends AbstractTestCase
{
    protected function setUpConfigurationObject()
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
    
        return new Puzzle\Configuration\Yaml(new Filesystem($adapter));
    }
}