<?php /** @noinspection StaticInvocationViaThisInspection */

namespace Tests\Unit\Columns;

use RuntimeException;
use Tests\PostyTestCase;
use Posty\Columns\Column;

use function Brain\Monkey\Functions\when;

class ColumnTest extends PostyTestCase
{

    /** @test */
    public function it_can_create_a_column_from_an_array(): void
    {
        $column = $this->createInstance();

        $this->assertEquals('Price', $column->getLabel());
        $this->assertEquals('£900', $column->getValue());
    }

    /** @test */
    public function it_throws_an_exception_if_creating_column_from_array_and_missing_data(): void
    {
        $this->expectException(RuntimeException::class);

        $this->createInstance([
            'label' => null,
            'value' => null
        ]);
    }

    /** @test */
    public function it_can_auto_generate_id_from_label(): void
    {
        $column = $this->createInstance();

        $this->assertEquals('Price', $column->getLabel());
        $this->assertEquals('price', $column->getId());
    }

    /** @test */
    public function it_can_manually_set_the_id(): void
    {
        $column = $this->createInstance([
            'id' => 'sale_price'
        ]);

        $this->assertEquals('Price', $column->getLabel());
        $this->assertEquals('sale_price', $column->getId());
    }

    /** @test */
    public function it_can_set_and_get_the_order(): void
    {
        $column = $this->createInstance();

        $column->setOrder(5);

        $this->assertEquals(5, $column->getOrder());
    }

    /** @test */
    public function it_can_set_and_get_the_label(): void
    {
        $column = $this->createInstance();

        $column->setLabel('Sale Price');

        $this->assertEquals('Sale Price', $column->getLabel());
    }

    /** @test */
    public function it_can_set_and_get_the_value(): void
    {
        $column = $this->createInstance();

        $column->setValue(fn () => '£250');

        $this->assertEquals('£250', $column->getValue());
    }

    /** @test */
    public function it_can_set_and_get_the_id(): void
    {
        $column = $this->createInstance();

        $column->setId('sale_price');

        $this->assertEquals('sale_price', $column->getId());
    }

    /** @test */
    public function it_can_set_both_the_label_and_id_at_once(): void
    {
        $column = $this->createInstance();

        $column->setLabelAndId('Sale Price', 'sale_price');

        $this->assertEquals('Sale Price', $column->getLabel());
        $this->assertEquals('sale_price', $column->getId());
    }

    /** @test */
    public function it_can_auto_generate_an_id_when_setting_both_label_and_id(): void
    {
        $column = $this->createInstance();

        when('sanitize_title')->justReturn('sale_price');

        $column->setLabelAndId('Sale Price');

        $this->assertEquals('Sale Price', $column->getLabel());
        $this->assertEquals('sale_price', $column->getId());
    }

    /** @test */
    public function it_can_order_column_alphabetically(): void
    {
        $column = $this->createInstance(['sort' => 'alpha']);

        $this->assertEquals('alpha', $column->getSortType());
    }

    /** @test */
    public function it_can_order_column_numerically(): void
    {
        $column = $this->createInstance(['sort' => 'numeric']);

        $this->assertEquals('numeric', $column->getSortType());
    }

    /**
     * Returns a new instance of Posty
     *
     * @param array $arguments
     * @return \PHPUnit\Framework\MockObject\MockBuilder|\Posty\Columns\Column
     */
    protected function createInstance(array $arguments = [])
    {
        when('sanitize_title')->justReturn('price');

        return Column::fromArray(array_merge(
            [
                'label' => 'Price',
                'value' => fn () => '£900'
            ],
            $arguments
        ));
    }
}