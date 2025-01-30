<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function test_that_basic_addition_works(): void
    {
        $result = 1 + 1;
        $this->assertEquals(2, $result);
    }
}
