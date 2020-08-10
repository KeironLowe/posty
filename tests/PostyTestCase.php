<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

use function Brain\Monkey\setUp;
use function Brain\Monkey\tearDown;

class PostyTestCase extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * Set's up the  tests
     *
     * @return void
     */
    public function setUp(): void {
        setUp();
    }

    /**
     * Tears down the tests
     *
     * @return void
     */
    public function tearDown(): void {
        tearDown();
    }

    /**
     * Asserts that an array doesn't contain an object which the given method
     * returns the provided value.
     *
     * @param array  $array
     * @param string $methodName
     * @param string $value
     * @return void
     */
    public function assertArrayDoesntContainsObjectWithMethodValue(array $array, string $methodName, string $value): void
    {
        foreach($array as $item) {
            if($item->$methodName() === $value) {
                self::assertTrue(false);
                return;
            }
        }
        self::assertTrue(true);
    }
}