<?php
namespace Eris\Generator;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testPicksStringsOfAMaximumLength()
    {
        $generator = new String(10);
        $lengths = [];
        $usedChars = [];
        for ($i = 0; $i < 1000; $i++) {
            $value = $generator();
            $length = strlen($value);
            $this->assertLessThanOrEqual(10, $length);
            $lengths = $this->accumulateLengths($lengths, $length);
            $usedChars = $this->accumulateUsedChars($usedChars, $value);
        }
        $this->assertEquals(11, count($lengths));
        // only readable characters
        $this->assertEquals(127 - 32, count($usedChars));
    }

    private function accumulateLengths(array $lengths, $length)
    {
        if (!isset($lengths[$length])) {
            $lengths[$length] = 0;
        }
        $lengths[$length]++;
        return $lengths;
    }

    private function accumulateUsedChars(array $usedChars, $value)
    {
        for ($j = 0; $j < strlen($value); $j++) {
            $char = $value{$j};
            if (!isset($usedChars[$char])) {
                $usedChars[$char] = 0;
            }
            $usedChars[$char]++;
        }
        return $usedChars;
    }
}