<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Tuple
 */
final class TupleTest extends TestCase
{
    /**
     * @testdox A tuple with w=1.0 is a point
     */
    public function test_a_tuple_with_w_equal_to_one_is_a_point(): void
    {
        $a = Tuple::point(4.3, -4.2, 3.1);

        $this->assertSame(4.3, $a->x());
        $this->assertSame(-4.2, $a->y());
        $this->assertSame(3.1, $a->z());
        $this->assertSame(1.0, $a->w());
        $this->assertTrue($a->isPoint());
        $this->assertFalse($a->isVector());
    }

    /**
     * @testdox A tuple with w=0.0 is a vector
     */
    public function test_a_tuple_with_w_equal_to_zero_is_a_vector(): void
    {
        $a = Tuple::vector(4.3, -4.2, 3.1);

        $this->assertSame(4.3, $a->x());
        $this->assertSame(-4.2, $a->y());
        $this->assertSame(3.1, $a->z());
        $this->assertSame(0.0, $a->w());
        $this->assertFalse($a->isPoint());
        $this->assertTrue($a->isVector());
    }

    public function test_a_vector_can_be_added_to_a_point(): void
    {
        $a = Tuple::point(3.0, -2.0, 5.0);
        $b = Tuple::vector(-2.0, 3.0, 1.0);

        $c = $a->plus($b);

        $this->assertSame(1.0, $c->x());
        $this->assertSame(1.0, $c->y());
        $this->assertSame(6.0, $c->z());
        $this->assertSame(1.0, $c->w());
    }

    public function test_a_vector_can_be_added_to_another_vector(): void
    {
        $a = Tuple::vector(3.0, -2.0, 5.0);
        $b = Tuple::vector(-2.0, 3.0, 1.0);

        $c = $a->plus($b);

        $this->assertSame(1.0, $c->x());
        $this->assertSame(1.0, $c->y());
        $this->assertSame(6.0, $c->z());
        $this->assertSame(0.0, $c->w());
    }

    public function test_a_point_cannot_be_added_to_a_point(): void
    {
        $a = Tuple::point(3.0, -2.0, 5.0);

        $this->expectException(RuntimeException::class);

        $a->plus($a);
    }

    public function test_subtracting_a_point_from_another_point_works(): void
    {
        $a = Tuple::point(3.0, 2.0, 1.0);
        $b = Tuple::point(5.0, 6.0, 7.0);

        $c = $a->minus($b);

        $this->assertSame(-2.0, $c->x());
        $this->assertSame(-4.0, $c->y());
        $this->assertSame(-6.0, $c->z());
        $this->assertSame(0.0, $c->w());
    }

    public function test_subtracting_a_vector_from_a_point_works(): void
    {
        $a = Tuple::point(3.0, 2.0, 1.0);
        $b = Tuple::vector(5.0, 6.0, 7.0);

        $c = $a->minus($b);

        $this->assertSame(-2.0, $c->x());
        $this->assertSame(-4.0, $c->y());
        $this->assertSame(-6.0, $c->z());
        $this->assertSame(1.0, $c->w());
    }

    public function test_subtracting_a_vector_from_another_vector_works(): void
    {
        $a = Tuple::vector(3.0, 2.0, 1.0);
        $b = Tuple::vector(5.0, 6.0, 7.0);

        $c = $a->minus($b);

        $this->assertSame(-2.0, $c->x());
        $this->assertSame(-4.0, $c->y());
        $this->assertSame(-6.0, $c->z());
        $this->assertSame(0.0, $c->w());
    }
}
