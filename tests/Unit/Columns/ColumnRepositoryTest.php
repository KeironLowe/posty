<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit\Columns;

use Mockery;
use RuntimeException;
use Tests\PostyTestCase;
use Posty\Columns\Column;
use Posty\Columns\ColumnRepository;

use function Brain\Monkey\Actions\expectAdded as expectActionAdded;
use function Brain\Monkey\Filters\expectAdded as expectFilterAdded;
use function Brain\Monkey\Functions\when;

class ColumnRepositoryTest extends PostyTestCase
{

    /** @test */
    public function can_get_the_columns(): void
    {
        $columns = $this->createInstance()->all();

        $this->assertContainsOnlyInstancesOf(Column::class, $columns);
    }

    /**
     * @test
     * @noinspection PhpParamsInspection
     */
    public function exception_is_thrown_if_column_data_is_not_an_array(): void
    {
        $this->expectException(RuntimeException::class);

        $columnRepository = $this->createInstance();

        $columnRepository->add('I should be an array or a closure returning an array.');
    }

    /** @test */
    public function can_add_a_single_column_from_an_array(): void
    {
        $columnRepository = $this->createInstance();

        $columnRepository->add([
            [
                'label' => 'Price',
                'value' => static fn () => '£900',
            ]
        ]);

        $columns = $columnRepository->all();
        $column = end($columns);

        $this->assertEquals('Price', $column->getLabel());
        $this->assertEquals('£900', $column->getValue());
    }

    /** @test */
    public function can_add_multiple_columns_from_an_array(): void
    {
        $columnRepository = $this->createInstance();

        $columnRepository->add([
            [
                'label' => 'Price',
                'value' => static fn () => '£900',
                'order' => 0
            ],
            [
                'label' => 'Photo',
                'value' => static fn () => 'photo.jpg',
                'order' => 1
            ]
        ]);

        $columns = $columnRepository->all();
        [$priceColumn, $photoColumn] = $columns;

        $this->assertEquals('£900', $priceColumn->getValue());
        $this->assertEquals('photo.jpg', $photoColumn->getValue());
    }

    /** @test */
    public function can_add_a_single_column_from_a_closure(): void
    {
        $columnRepository = $this->createInstance();

        $columnRepository->add(static function () {
            return [
                [
                    'label' => 'Price',
                    'value' => static fn() => '£900'
                ]
            ];
        });

        $columns = $columnRepository->all();
        $column = end($columns);

        $this->assertEquals('Price', $column->getLabel());
        $this->assertEquals('£900', $column->getValue());
    }

    /** @test */
    public function can_add_a_multiple_columns_from_a_closure(): void
    {
        $columnRepository = $this->createInstance();

        $columnRepository->add(static function () {
            return [
                [
                    'label' => 'Price',
                    'value' => static fn() => '£900',
                    'order' => 0,
                ],
                [
                    'label' => 'Photo',
                    'value' => static fn () => 'photo.jpg',
                    'order' => 1
                ]
            ];
        });

        $columns = $columnRepository->all();
        [$priceColumn, $photoColumn] = $columns;

        $this->assertEquals('£900', $priceColumn->getValue());
        $this->assertEquals('photo.jpg', $photoColumn->getValue());
    }

    /** @test */
    public function can_add_a_column_at_specified_index(): void
    {
        $columnRepository = $this->createInstance();

        $columnRepository->add([
            [
                'label' => 'Price',
                'value' => static fn () => '£900',
                'order' => 0
            ]
        ]);

        $column = $columnRepository->all()[0];

        $this->assertEquals('Price', $column->getLabel());
        $this->assertEquals('£900', $column->getValue());
        $this->assertEquals(0, $column->getOrder());
    }

    /** @test */
    public function can_remove_a_single_column_from_an_array(): void
    {
        $columns = $this->createInstance();

        $columns->remove(['title']);

        $this->assertArrayDoesntContainsObjectWithMethodValue($columns->all(), 'getLabel', 'Title');
    }

    /** @test */
    public function can_remove_multiple_columns_from_an_array(): void
    {
        $columns = $this->createInstance();

        $columns->remove(['title', 'date']);

        $this->assertArrayDoesntContainsObjectWithMethodValue($columns->all(), 'getLabel', 'Title');
        $this->assertArrayDoesntContainsObjectWithMethodValue($columns->all(), 'getLabel', 'Date');
    }

    /** @test */
    public function can_remove_a_single_column_from_a_closure(): void
    {
        $columns = $this->createInstance();

        $columns->remove(static function () {
            return ['title'];
        });

        $this->assertArrayDoesntContainsObjectWithMethodValue($columns->all(), 'getLabel', 'Title');
    }

    /** @test */
    public function can_remove_multiple_columns_from_a_closure(): void
    {
        $columns = $this->createInstance();

        $columns->remove(static function () {
            return ['title', 'date'];
        });

        $this->assertArrayDoesntContainsObjectWithMethodValue($columns->all(), 'getLabel', 'Title');
        $this->assertArrayDoesntContainsObjectWithMethodValue($columns->all(), 'getLabel', 'Date');
    }

    /** @test */
    public function can_reorder_columns_from_an_array(): void
    {
        $expectedOrder = [
            'cb',
            'date',
            'author',
            'title'
        ];

        $instance = $this->createInstance()->reorder($expectedOrder);

        $columns = $instance->all();

        $this->assertEquals(
            $expectedOrder,
            [
                $columns[0]->getId(),
                $columns[1]->getId(),
                $columns[2]->getId(),
                $columns[3]->getId()
            ]
        );
    }

    /** @test */
    public function can_reorder_columns_from_a_closure(): void
    {
        $expectedOrder = [
            'cb',
            'date',
            'author',
            'title'
        ];

        $instance = $this->createInstance()->reorder(static function() use ($expectedOrder) {
            return $expectedOrder;
        });

        $columns = $instance->all();

        $this->assertEquals(
            $expectedOrder,
            [
                $columns[0]->getId(),
                $columns[1]->getId(),
                $columns[2]->getId(),
                $columns[3]->getId()
            ]
        );
    }

    /** @test */
    public function reordering_columns_keeps_missing_items_from_order_list(): void
    {
        $instance = $this->createInstance()->reorder([
            'cb',
        ]);

        $columns = $instance->all();

        $this->assertEquals(
            [
                'cb',
                'title',
                'author',
                'date'
            ],
            [
                $columns[0]->getId(),
                $columns[1]->getId(),
                $columns[2]->getId(),
                $columns[3]->getId()
            ]
        );
    }

    /** @test */
    public function it_can_register(): void
    {
        $instance = $this->createInstance(true)->shouldAllowMockingProtectedMethods()->makePartial();

        $instance->add([
            [
                'label' => 'Price',
                'value' => static fn () => '£900',
                'id'    => 'price'
            ]
        ]);

        // Register the columns
        $instance->shouldReceive('getHeadings')->once()->withNoArgs();
        expectFilterAdded('manage_products_posts_columns')
            ->once()
            ->with(Mockery::type('Closure'))
            ->whenHappen(static function (callable $callback) {
                $callback();
            });

        // Populate the column values
        expectActionAdded('manage_products_posts_custom_column')
            ->once()
            ->with(Mockery::type('Closure'), 10, 2)
            ->whenHappen(static function (callable $callback) {
                $callback('price', 1);
            });
        $this->expectOutputString('£900');

        $instance->register();
    }

    /** @test */
    public function it_throws_an_exception_if_a_column_cant_be_found(): void
    {
        $instance = $this->createInstance(true)->shouldAllowMockingProtectedMethods()->makePartial();

        $this->expectException(RuntimeException::class);

        // Populate the column values
        expectActionAdded('manage_products_posts_custom_column')
            ->once()
            ->with(Mockery::type('Closure'), 10, 2)
            ->whenHappen(static function (callable $callback) {
                $callback('price', 1);
            });

        $instance->register();
    }

    /** @test */
    public function it_can_get_column_headings(): void
    {
        $instance = $this->createInstance(true)->shouldAllowMockingProtectedMethods()->makePartial();

        $instance->add([
            [
                'label' => 'Price',
                'value' => static fn () => '£900',
                'id'    => 'price'
            ]
        ]);

        $this->assertEquals(
            [
                'cb' => '<input type="checkbox" />',
                'title' => 'Title',
                'author' => 'Author',
                'date' => 'Date',
                'price' => 'Price'
            ],
            $instance->getHeadings()
        );
    }

    /**
     * Returns a new instance of Posty
     *
     * @param bool  $mocked
     * @return \Mockery\MockInterface|\Posty\Columns\ColumnRepository
     */
    protected function createInstance(bool $mocked = false)
    {
        when('sanitize_title')->justReturn('anything');

        if($mocked) {
            return Mockery::mock(ColumnRepository::class, ['products']);
        }

        return new ColumnRepository('products');
    }
}