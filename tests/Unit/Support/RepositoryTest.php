<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit\Support;

use Posty\Support\Repository;
use Tests\PostyTestCase;

class RepositoryTest extends PostyTestCase
{

    /** @test */
    public function can_check_if_item_at_index_exists(): void
    {
        $repository = new class extends Repository {};
        $repository->items = [1, 2, 3, 4, 5];

        $this->assertTrue($repository->offsetExists(0));
    }

    /** @test */
    public function can_get_item_at_index(): void
    {
        $repository = new class extends Repository {};
        $repository->items = [1, 2, 3, 4, 5];

        $this->assertEquals(1, $repository->offsetGet(0));
    }

    /** @test */
    public function can_set_item_at_index(): void
    {
        $repository = new class extends Repository {};

        $repository->offsetSet(0, 1);

        $this->assertEquals(1, $repository[0]);
    }

    /** @test */
    public function can_set_item_without_an_index(): void
    {
        $repository = new class extends Repository {};
        $repository->items = [1, 2, 3, 4, 5];

        $repository->offsetSet(null, 6);

        $this->assertEquals(6, $repository[5]);
    }

    /** @test */
    public function can_remove_item_at_index(): void
    {
        $repository = new class extends Repository {};
        $repository->items = [1, 2, 3, 4, 5];

        $repository->offsetUnset(0);

        $this->assertNull($repository[0]);
    }
}