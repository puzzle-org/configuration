<?php

namespace Puzzle\Configuration;

require_once __DIR__ . '/AbstractTestCase.php';

class MemoryTest extends \AbstractTestCase
{
    protected function setUpConfigurationObject()
    {
        $values = array(
            'a/b/c' => 'abc',
            'a/b/d' => 'abd',
            'b/b/c' => 'bbc',
            'd/e/f' => 'def',
        );

        return new Memory($values);
    }
}
