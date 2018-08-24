<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use Gaufrette\Filesystem;
use Gaufrette\Exception\FileNotFound;

class Yaml extends AbstractConfiguration
{
    private const EXTENSION = 'yml';

    private
        $cache,
        $storage;
        
    public function __construct(Filesystem $configurationFilesStorage)
    {
        parent::__construct();
        
        $this->cache = [];
        $this->storage = $configurationFilesStorage;
    }
    
    public function exists(string $fqn): bool
    {
        return $this->getValue($fqn) !== null;
    }

    protected function getValue(string $fqn)
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
    
    private function getYaml(string $alias): array
    {
        if(! isset($this->cache[$alias]))
        {
            $fileContent = $this->storage->read($this->computeFilename($alias));
            $this->cache[$alias] = $this->parseYaml($fileContent);
        }
        
        return $this->cache[$alias];
    }

    private function parseYaml(string $content): array
    {
        $decodedYaml = \Symfony\Component\Yaml\Yaml::parse($content);
        if(! is_array($decodedYaml))
        {
            $decodedYaml = [];
        }

        return $decodedYaml;
    }

    private function computeFilename(string $alias): string
    {
        return $alias . "." . self::EXTENSION;
    }

    private function computeAlias(string $filename): string
    {
        return pathinfo($filename, PATHINFO_FILENAME);
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

    public function all(): iterable
    {
        $this->populateCache();

        return $this->flatten(
            // remove empty files
            array_filter($this->cache)
        );
    }

    private function populateCache(): void
    {
        $files = $this->storage->keys();

        foreach($files as $file)
        {
            if(pathinfo($file, PATHINFO_EXTENSION) === self::EXTENSION)
            {
                $this->getYaml(
                    $this->computeAlias($file)
                );
            }
        }
    }
}
