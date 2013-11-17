<?php

namespace Puzzle\Configuration;

class Yaml extends AbstractConfiguration
{
    private
        $cache,
        $directory;
        
    public function __construct($configurationFilesDirectory = null)
    {
        parent::__construct();
        
        $this->cache = array();
        
        if($configurationFilesDirectory === null)
        {
            $configurationFilesDirectory = __DIR__ . '/../../../config/';
        }
        
        $this->directory = rtrim($configurationFilesDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
    
    public function exists($fqn)
    {
        return $this->getValue($fqn) !== null;
    }

    protected function getValue($fqn)
    {
        $keys = $this->parseDsn($fqn);
        $filename = array_shift($keys);

        $config = $this->getYaml($filename);
        
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

    private function getYaml($alias)
    {
        if(! isset($this->cache[$alias]))
        {
            $filename = $this->computeFilename($alias);
            $this->cache[$alias] = \Symfony\Component\Yaml\Yaml::parse($filename);
        }
        
        return $this->cache[$alias];
    }

    private function computeFilename($alias)
    {
        return $this->directory . $alias . '.yml';
    }
}
