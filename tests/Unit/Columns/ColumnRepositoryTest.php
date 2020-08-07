<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit\Columns;

use WP_Mock;
use Tests\PostyTestCase;
use Posty\Columns\Column;
use Posty\Columns\ColumnRepository;

class ColumnRepositoryTest extends PostyTestCase
{

    /** @test */
    public function can_get_the_columns(): void
    {
        $columns = $this->createInstance()->all();

        $this->assertContainsOnlyInstancesOf(Column::class, $columns);
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

    /**
     * Returns a new instance of Posty
     *
     * @param bool  $mocked
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\Posty\Columns\ColumnRepository
     */
    protected function createInstance(bool $mocked = false)
    {
        WP_Mock::userFunction( 'sanitize_title')->andReturn('return value');

        if($mocked) {
            return $this->getMockBuilder(ColumnRepository::class);
        }

        return new ColumnRepository();
    }
}