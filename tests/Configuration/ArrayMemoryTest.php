<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use Puzzle\Configuration;

require_once __DIR__ . '/AbstractTestCase.php';

final class ArrayMemoryTest extends AbstractTestCase
{
    protected function setUpConfigurationObject(): Configuration
    {
        $values = [
            'a' => [
                'b' => [
                    'c' => 'abc',
                    'd' => 'abd',
                ],
                'x' => [
                    'y' => 'axy',
                ]
            ],
            'b' => [
                'b' => [
                    'c' => 'bbc'
                ],
            ],
            'd' => [
                'e' => [
                    'f' => 'def'
                ],
            ],
            'locale' => [
                'front' => ['en', 'fr'],
                'back' => ['de', 'fr', 'it'],
            ],
        ];

        return new ArrayMemory($values);
    }

    public function testReadAsArray(): void
    {
        $values = $this->config->readRequired('a/b');

        $expected = [
            'c'  => 'abc',
            'd'  => 'abd',
        ];

        self::assertSame($expected, $values);
    }
}
