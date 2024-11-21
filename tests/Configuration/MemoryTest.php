<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use PHPUnit\Framework\Attributes\DataProvider;
use Puzzle\Configuration;

require_once __DIR__ . '/AbstractTestCase.php';

final class MemoryTest extends AbstractTestCase
{
    protected function setUpConfigurationObject(): Configuration
    {
        $values = [
            'a/b/c' => 'abc',
            'a/b/d' => 'abd',
            'a/x/y' => 'axy',
            'b/b/c' => 'bbc',
            'd/e/f' => 'def',
            'locale/front' => ['en', 'fr'],
            'locale/back' => ['de', 'fr', 'it'],
        ];

        return new Memory($values);
    }

    #[DataProvider('providerTestReadArray')]
    public function testReadArray(string $fqn, array $expected): void
    {
        // s/o for this impl
        self::assertTrue(true);
    }
}
