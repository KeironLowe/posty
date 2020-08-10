<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit\Support;

use Posty\Support\Arr;
use Tests\PostyTestCase;

class ArrTest extends PostyTestCase
{

    /** @test */
    public function can_get_array_index_from_a_closure(): void
    {
        $index = Arr::getIndexWhere(
            fn ($item) => $item['id'] === 3,
            [
                [
                    'id' => 1
                ],
                [
                    'id' => 2
                ],
                [
                    'id' => 3
                ]
            ]
        );

        $this->assertEquals(2, $index);
    }

    /** @test */
    public function get_index_where_returns_null_if_no_matches(): void
    {
        $index = Arr::getIndexWhere(
            fn ($item) => $item['id'] === 10,
            [
                [
                    'id' => 1
                ]
            ]
        );
        
        $this->assertNull($index);
    }

    /** @test */
    public function can_insert_items_at_the_start_of_the_array(): void
    {
        $array = ['Cat', 'Dog'];

        $array = Arr::insert('Mouse', 0, $array);

        $this->assertEquals('Mouse', $array[0]);
    }

    /** @test */
    public function can_insert_items_at_end_start_of_the_array(): void
    {
        $array = ['Cat', 'Dog'];

        $array = Arr::insert('Mouse', 2, $array);

        $this->assertEquals('Mouse', end($array));
    }

    /** @test */
    public function can_insert_items_between_the_start_and_end_of_array(): void
    {
        $array = ['Cat', 'Dog'];

        $array = Arr::insert('Mouse', 1, $array);

        $this->assertEquals('Mouse', $array[1]);
    }

    /** @test */
    public function can_find_items_which_match_the_given_condition(): void
    {
        $item = Arr::findWhere(
            fn ($item) => $item['id'] === 2,
            [
                [
                    'id' => 1,
                    'title' => 'Test Title'
                ],
                [
                    'id' => 2,
                    'title' => 'Another Test Title'
                ]
            ]
        );

        $this->assertEquals('Another Test Title', $item['title']);
    }
    
    /** @test */
    public function find_where_returns_null_if_no_matches(): void
    {
        $item = Arr::findWhere(
            fn ($item) => $item['id'] === 10,
            [
                [
                    'id' => 1,
                    'title' => 'Test Title'
                ]
            ]
        );
        
        $this->assertNull($item);
    }
}