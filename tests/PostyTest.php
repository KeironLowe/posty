<?php

namespace Tests;

use WP_Mock;
use Posty\Posty;
use WP_Mock\Functions;
use WP_Mock\Tools\TestCase;

class PostyTest extends TestCase
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

    /** @test */
    public function canCreateAnInstance(): void
    {
        self::assertInstanceOf(Posty::class, $this->createInstance(false));
    }

    /** @test */
    public function staticMakeReturnsAnInstance(): void
    {
        self::assertInstanceOf(Posty::class, $this->createInstance());
    }

    /** @test */
    public function canRegisterPostType(): void
    {

    }

    /** @test */
    public function canSetLabels(): void
    {

    }

    /** @test */
    public function canSetArguments(): void
    {

    }

    /** @test */
    public function canAddASingleColumnWithoutAnOrder(): void
    {
        WP_Mock::expectFilterAdded( 'manage_products_posts_columns', Functions::type( 'closure' ));
        WP_Mock::expectActionAdded('manage_products_posts_custom_column', Functions::type('closure'), 10, 2);
        //WP_Mock::expectFilterNotAdded('manage_products_posts_columns', Functions::type('closure'));

        $this->createInstance()->addColumn('test_column', static function () { /* */ });

        $this->assertConditionsMet();
    }

    /** @test */
    public function canAddASingleColumnWithOrder(): void
    {
        //WP_Mock::expectFilterAdded( 'manage_products_posts_columns', Functions::type( 'closure' ) );
        //WP_Mock::expectActionAdded('manage_products_posts_custom_column', Functions::type('closure'), 10, 2);
        //
        //$this->createInstance()->addColumn('test_column', static function () { /* */ });
        //
        //$this->assertConditionsMet();
    }

    /** @test */
    public function canAddMultipleColumns(): void
    {

    }

    /** @test */
    public function canRemoveASingleColumn(): void
    {

    }

    /** @test */
    public function canRemoveMultipleColumns(): void
    {

    }

    /** @test */
    public function canReorderColumns(): void
    {

    }

    /**
     * Returns a new instance of Posty
     *
     * @param bool $static
     * @return \Posty\Posty
     */
    protected function createInstance(bool $static = true)
    {
        WP_Mock::userFunction( 'sanitize_title');

        if(!$static) {
            return new Posty('products', 'Product', 'Products');
        }

        return Posty::make('products', 'Product', 'Products');
    }
}