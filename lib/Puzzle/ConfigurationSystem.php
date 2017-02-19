<?php

namespace Puzzle;

interface ConfigurationSystem
{
    /**
     * Add a more prioritary configuration to create a fallback system between configurations
     *
     * @param Configuration $configuration
     *
     * @return self
     */
    public function overrideBy(Configuration $configuration);

    /**
     * Add a less prioritary configuration to create a fallback system between configurations
     *
     * @param Configuration $configuration
     *
     * @return self
     */
    public function addBase(Configuration $configuration);
}
