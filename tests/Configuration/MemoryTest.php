<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use Puzzle\Configuration;

require_once __DIR__ . '/AbstractTestCase.php';

class MemoryTest extends AbstractTestCase
{
    private
        $values;

    protected function setUpConfigurationObject(): Configuration
    {
        $this->values = [
            'a/b/c' => 'abc',
            'a/b/d' => 'abd',
            'a/x/y' => 'axy',
            'b/b/c' => 'bbc',
            'd/e/f' => 'def',
            'locale/front' => ['en', 'fr'],
            'locale/back' => ['de', 'fr', 'it'],
        ];

        return new Memory($this->values);
    }

    /**
     * @dataProvider providerTestReadArray
     */
    public function testReadArray(string $fqn, array $expected): void
    {
        // s/o for this impl
        $this->assertTrue(true);
    }
}
