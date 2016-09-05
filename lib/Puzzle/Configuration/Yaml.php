<?php

namespace Puzzle\Configuration;

use Gaufrette\Filesystem;
use Gaufrette\Exception\FileNotFound;

class Yaml extends AbstractConfiguration
{
    private
        $cache,
        $storage;
        
    public function __construct(Filesystem $configurationFilesStorage)
    {
        parent::__construct();
        
        $this->cache = array();
        $this->storage = $configurationFilesStorage;
    }
    
    public function exists($fqn)
    {
        return $this->getValue($fqn) !== null;
    }

    protected function getValue($fqn)
    {
        $keys = $this->parseDsn($fqn);
        $filename = array_shift($keys);

        try
        {
            $config = $this->getYaml($filename);
        }
        catch(FileNotFound $e)
        {
            return null;
        }
        
        return $this->readValue($keys, $config);
    }
    
    private function getYaml($alias)
    {
        if(! isset($this->cache[$alias]))
        {
            $fileContent = $this->storage->read($this->computeFilename($alias));
            $this->cache[$alias] = $this->parseYaml($fileContent);
        }
        
        return $this->cache[$alias];
    }

    private function parseYaml($content)
    {
        $decodedYaml = \Symfony\Component\Yaml\Yaml::parse($content);
        if(! is_array($decodedYaml))
        {
            $decodedYaml = array();
        }

        return $decodedYaml;
    }

    private function computeFilename($alias)
    {
        return $alias . '.yml';
    }
    
    private function readValue(array $keys, array $config)
    {
        while(! empty($keys))
        {
            $key = array_shift($keys);
            
            if(!isset($config[$key]))
            {
                return null;
            }
            
            $config = $config[$key];
        }
        
        return $config;
    }
}
