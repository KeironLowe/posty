<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit\Columns;

use Tests\PostyTestCase;
use Posty\Columns\Column;
use WP_Mock;

class ColumnTest extends PostyTestCase
{

    /** @test */
    public function can_set_as_a_default_column(): void
    {
        $instance = $this->createInstance();

        $this->assertFalse($instance->isADefaultField());

        $instance->setAsADefaultField();

        $this->assertTrue($instance->isADefaultField());
    }

    /**
     * Returns a new instance of Posty
     *
     * @param bool  $mocked
     * @param array $arguments
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\Posty\Columns\Column
     */
    protected function createInstance(bool $mocked = false, array $arguments = [])
    {
        WP_Mock::userFunction( 'sanitize_title')->andReturn('price');

        if($mocked) {
            return $this->getMockBuilder(Column::class)
                ->setConstructorArgs(['Price', ...$arguments]);
        }

        return new Column('Price', ...$arguments);
    }
}