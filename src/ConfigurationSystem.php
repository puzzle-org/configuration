<?php

declare(strict_types = 1);

namespace Puzzle;

interface ConfigurationSystem
{
    /**
     * Add a more prioritary configuration to create a fallback system between configurations
     */
    public function overrideBy(Configuration $configuration): ConfigurationSystem;

    /**
     * Add a less prioritary configuration to create a fallback system between configurations
     */
    public function addBase(Configuration $configuration): ConfigurationSystem;
}
