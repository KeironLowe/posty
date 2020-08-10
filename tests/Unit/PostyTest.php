<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit;

use Posty\Posty;
use RuntimeException;
use Tests\PostyTestCase;
use Posty\Columns\ColumnRepository;

use function Brain\Monkey\Functions\when;
use function Brain\Monkey\Functions\expect;

class PostyTest extends PostyTestCase
{

    /** @test */
    public function can_auto_generate_post_type_slug(): void
    {
        $instance = $this->createInstance();

        $this->assertEquals('products', $instance->getArguments()['rewrite']['slug']);
    }

    /** @test */
    public function can_manually_set_post_type_slug(): void
    {
        when('sanitize_title')->justReturn('products');

        $instance = new Posty('Product', 'Products', 'sale_products');

        $this->assertEquals('sale_products', $instance->getArguments()['rewrite']['slug']);
    }

    /** @test */
    public function can_get_the_labels(): void
    {
        $labels = $this->createInstance()->getLabels();

        $this->assertEquals('Products', $labels['name']);
        $this->assertEquals('Product', $labels['singular_name']);
        $this->assertEquals('Add New Product', $labels['add_new_item']);
    }

    /**
     * @test
     * @noinspection PhpParamsInspection
     */
    public function exception_is_thrown_if_arguments_arent_an_array(): void
    {
        $this->expectException(RuntimeException::class);

        $this->createInstance()->setArguments('I should be an array or closure returning an array');
    }

    /** @test */
    public function can_set_the_labels_from_an_array(): void
    {
        $instance = $this->createInstance()->setLabels([
            'name' => 'Pages',
            'singular_name' => 'Page'
        ]);

        $labels = $instance->getLabels();

        $this->assertEquals('Pages', $labels['name']);
        $this->assertEquals('Page', $labels['singular_name']);
        $this->assertArrayNotHasKey('add_new_item', $labels);
    }

    /**
     * @test
     * @noinspection PhpParamsInspection
     */
    public function exception_is_thrown_if_labels_arent_an_array(): void
    {
        $this->expectException(RuntimeException::class);

        $this->createInstance()->setLabels('I should be an array or closure returning an array');
    }

    /** @test */
    public function can_set_the_labels_from_a_closure(): void
    {
        $instance = $this->createInstance()->setLabels(static function () {
            return [
                'name'          => 'Pages',
                'singular_name' => 'Page',
            ];
        });

        $labels = $instance->getLabels();

        $this->assertEquals('Pages', $labels['name']);
        $this->assertEquals('Page', $labels['singular_name']);
        $this->assertArrayNotHasKey('add_new_item', $labels);
    }

    /** @test */
    public function the_existing_labels_are_passed_as_an_argument_to_the_set_labels_closure(): void
    {
        $instance = $this->createInstance()->setLabels(static function (array $existingLabels) {
            $existingLabels['name'] = 'Pages';
            $existingLabels['singular_name'] = 'Page';

            return $existingLabels;
        });

        $labels = $instance->getLabels();

        $this->assertEquals('Pages', $labels['name']);
        $this->assertEquals('Page', $labels['singular_name']);
        $this->assertEquals('Add New Product', $labels['add_new_item']);
    }

    /** @test */
    public function get_can_the_arguments(): void
    {
        $instance  = $this->createInstance();
        $arguments = $instance->getArguments();

        $this->assertEquals(true, $arguments['public']);
        $this->assertEquals($instance->getLabels(), $arguments['labels']);
    }

    /** @test */
    public function get_set_the_arguments_from_an_array(): void
    {
        $instance = $this->createInstance()->setArguments([
            'public' => false,
            'supports' => ['revisions']
        ]);

        $arguments = $instance->getArguments();

        $this->assertEquals(false, $arguments['public']);
        $this->assertEquals(['revisions'], $arguments['supports']);
        $this->assertArrayNotHasKey('has_archive', $arguments);
    }

    /** @test */
    public function get_set_the_arguments_from_a_closure(): void
    {
        $instance = $this->createInstance()->setArguments(static function () {
            return [
                'public'   => false,
                'supports' => ['revisions']
            ];
        });

        $arguments = $instance->getArguments();

        $this->assertEquals(false, $arguments['public']);
        $this->assertEquals(['revisions'], $arguments['supports']);
        $this->assertArrayNotHasKey('has_archive', $arguments);
    }

    /** @test */
    public function the_existing_arguments_are_passed_as_an_argument_to_the_set_arguments_closure(): void
    {
        $instance = $this->createInstance()->setArguments(static function (array $existingArguments) {
            $existingArguments['public'] = false;
            $existingArguments['supports'] = ['revisions'];

            return $existingArguments;
        });

        $arguments = $instance->getArguments();

        $this->assertEquals(false, $arguments['public']);
        $this->assertEquals(['revisions'], $arguments['supports']);
        $this->assertEquals(true, $arguments['has_archive']);
    }

    /** @test */
    public function can_get_the_columns(): void
    {
        $posty = $this->createInstance();

        $this->assertInstanceOf(ColumnRepository::class, $posty->columns());
    }

    /** */
    public function it_can_register(): void
    {
        expect('register_post_type')->once();

        $this->createInstance()->register();

        $this->assertTrue(has_action('init', 'function ()'));
    }

    /** */
    public function it_can_register_columns(): void
    {
        //WP_Mock::expectActionAdded('init', Functions::type('Closure'));

        $columns = $this->createMock(ColumnRepository::class);
        $columns->expects($this->once())->method('register');

        $instance = $this->createInstance();

        $instance->columns($columns);
        $instance->register();

        $this->assertConditionsMet();
    }

    /**
     * Returns a new instance of Posty
     *
     * @param bool $mocked
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\Posty\Posty
     */
    protected function createInstance(bool $mocked = false)
    {
        when('sanitize_title')->justReturn('products');

        if($mocked) {
            return $this->getMockBuilder(Posty::class)
                ->setConstructorArgs(['Product', 'Products']);
        }

        return new Posty('Product', 'Products');
    }
}