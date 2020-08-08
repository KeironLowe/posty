<?php

namespace Tests;

use WP_Mock;
use WP_Mock\Tools\TestCase;

class PostyTestCase extends TestCase
{

    /**
     * Set's up the  tests
     *
     * @return void
     */
    public function setUp(): void {
        WP_Mock::setUp();
    }

    /**
     * Tears down the tests
     *
     * @return void
     */
    public function tearDown(): void {
        WP_Mock::tearDown();
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