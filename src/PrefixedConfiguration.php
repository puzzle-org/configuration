<?php

declare(strict_types = 1);

namespace Puzzle;

class PrefixedConfiguration implements Configuration
{
    private ?string
        $prefix;
    private Configuration
        $configuration;

    public function __construct(Configuration $configuration, ?string $prefix = null)
    {
        $this->prefix = null;
        $this->setPrefix($prefix);
        $this->configuration = $configuration;
    }

    public function setPrefix(?string $prefix): self
    {
        if($this->isValidPrefix($prefix))
        {
            $this->prefix = $this->trimPrefix($prefix);
        }

        return $this;
    }

    private function isValidPrefix(?string $prefix): bool
    {
        if(is_string($prefix))
        {
            $prefix = $this->trimPrefix($prefix);

            return ! empty($prefix);
        }

        return false;
    }

    private function trimPrefix(string $prefix): string
    {
        return trim($prefix, self::SEPARATOR);
    }

    public function read(string $fqn, mixed $defaultValue = null): mixed
    {
        return $this->configuration->read($this->computeFqn($fqn), $defaultValue);
    }

    public function readRequired(string $fqn): mixed
    {
        return $this->configuration->readRequired($this->computeFqn($fqn));
    }

    public function readFirstExisting(string ...$fqns): mixed
    {
        $fqns = array_map(function(string $fqn): string {
            return $this->computeFqn($fqn);
        }, $fqns);

        return $this->configuration->readFirstExisting(...$fqns);
    }

    public function exists(string $fqn): bool
    {
        return $this->configuration->exists($this->computeFqn($fqn));
    }

    private function computeFqn(string $fqn): string
    {
        if($this->prefix !== null)
        {
            $fqn = $this->prefix . self::SEPARATOR . ltrim($fqn, self::SEPARATOR);
        }

        return $fqn;
    }

    public function all(): iterable
    {
        return [];
    }
}
