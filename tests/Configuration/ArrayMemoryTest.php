<?php

declare(strict_types = 1);

namespace Puzzle\Configuration;

use Puzzle\Configuration;

require_once __DIR__ . '/AbstractTestCase.php';

class ArrayMemoryTest extends AbstractTestCase
{
    protected function setUpConfigurationObject(): Configuration
    {
        $values = [
            'a' => [
                'b' => [
                    'c' => 'abc',
                    'd' => 'abd',
                ],
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

        $this->assertSame($expected, $values);
    }
}
