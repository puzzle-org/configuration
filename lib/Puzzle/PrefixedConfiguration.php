<?php

namespace Puzzle;

use Puzzle\Configuration;

class PrefixedConfiguration implements Configuration
{
    private
        $prefix,
        $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->prefix = null;
        $this->configuration = $configuration;
    }

    public function setPrefix($prefix)
    {
        if($this->isValidPrefix($prefix))
        {
            $this->prefix = $this->trimPrefix($prefix);
        }

        return true;
    }

    private function isValidPrefix($prefix)
    {
        if(is_string($prefix))
        {
            $prefix = $this->trimPrefix($prefix);

            return (! empty($prefix));
        }

        return false;
    }

    private function trimPrefix($prefix)
    {
        return trim($prefix, self::SEPARATOR);
    }

    public function read($fqn, $defaultValue = null)
    {
        return $this->configuration->read($this->computeFqn($fqn), $defaultValue);
    }

    public function readRequired($fqn)
    {
        return $this->configuration->readRequired($this->computeFqn($fqn));
    }

    public function readFirstExisting()
    {
        $keys = func_get_args();
        $args = array();

        foreach($keys as $fqn)
        {
            $args[] = $this->computeFqn($fqn);
        }

        return call_user_func_array(array($this->configuration, __FUNCTION__), $args);
    }

    public function exists($fqn)
    {
        return $this->configuration->exists($this->computeFqn($fqn));
    }

    private function computeFqn($fqn)
    {
        if($this->prefix !== null)
        {
            $fqn = $this->prefix . self::SEPARATOR . ltrim($fqn, self::SEPARATOR);
        }

        return $fqn;
    }
}