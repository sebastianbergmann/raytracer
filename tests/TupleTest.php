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
        $p = Tuple::createPoint(4.3, -4.2, 3.1);

        $this->assertSame(4.3, $p->x());
        $this->assertSame(-4.2, $p->y());
        $this->assertSame(3.1, $p->z());
        $this->assertSame(1.0, $p->w());
        $this->assertTrue($p->isPoint());
        $this->assertFalse($p->isVector());
    }

    /**
     * @testdox A tuple with w=0.0 is a vector
     */
    public function test_a_tuple_with_w_equal_to_zero_is_a_vector(): void
    {
        $v = Tuple::createVector(4.3, -4.2, 3.1);

        $this->assertSame(4.3, $v->x());
        $this->assertSame(-4.2, $v->y());
        $this->assertSame(3.1, $v->z());
        $this->assertSame(0.0, $v->w());
        $this->assertFalse($v->isPoint());
        $this->assertTrue($v->isVector());
    }

    /**
     * @testdox A tuple with w!=1.0 and w!=0.0 is neither a point nor a vector
     */
    public function test_can_be_generic(): void
    {
        $t = Tuple::create(1.0, -2.0, 3.0, -4.0);

        $this->assertSame(1.0, $t->x());
        $this->assertSame(-2.0, $t->y());
        $this->assertSame(3.0, $t->z());
        $this->assertSame(-4.0, $t->w());
        $this->assertFalse($t->isPoint());
        $this->assertFalse($t->isVector());
    }

    public function test_a_vector_can_be_added_to_a_point(): void
    {
        $p1 = Tuple::createPoint(3.0, -2.0, 5.0);
        $v  = Tuple::createVector(-2.0, 3.0, 1.0);

        $p2 = $p1->plus($v);

        $this->assertSame(1.0, $p2->x());
        $this->assertSame(1.0, $p2->y());
        $this->assertSame(6.0, $p2->z());
        $this->assertSame(1.0, $p2->w());
    }

    public function test_a_vector_can_be_added_to_another_vector(): void
    {
        $v1 = Tuple::createVector(3.0, -2.0, 5.0);
        $v2 = Tuple::createVector(-2.0, 3.0, 1.0);

        $v3 = $v1->plus($v2);

        $this->assertSame(1.0, $v3->x());
        $this->assertSame(1.0, $v3->y());
        $this->assertSame(6.0, $v3->z());
        $this->assertSame(0.0, $v3->w());
    }

    public function test_a_point_cannot_be_added_to_a_point(): void
    {
        $p = Tuple::createPoint(3.0, -2.0, 5.0);

        $this->expectException(RuntimeException::class);

        $p->plus($p);
    }

    public function test_subtracting_a_point_from_another_point_works(): void
    {
        $p1 = Tuple::createPoint(3.0, 2.0, 1.0);
        $p2 = Tuple::createPoint(5.0, 6.0, 7.0);

        $v = $p1->minus($p2);

        $this->assertSame(-2.0, $v->x());
        $this->assertSame(-4.0, $v->y());
        $this->assertSame(-6.0, $v->z());
        $this->assertSame(0.0, $v->w());
    }

    public function test_subtracting_a_vector_from_a_point_works(): void
    {
        $p1 = Tuple::createPoint(3.0, 2.0, 1.0);
        $v  = Tuple::createVector(5.0, 6.0, 7.0);

        $p2 = $p1->minus($v);

        $this->assertSame(-2.0, $p2->x());
        $this->assertSame(-4.0, $p2->y());
        $this->assertSame(-6.0, $p2->z());
        $this->assertSame(1.0, $p2->w());
    }

    public function test_subtracting_a_vector_from_another_vector_works(): void
    {
        $v1 = Tuple::createVector(3.0, 2.0, 1.0);
        $v2 = Tuple::createVector(5.0, 6.0, 7.0);

        $v3 = $v1->minus($v2);

        $this->assertSame(-2.0, $v3->x());
        $this->assertSame(-4.0, $v3->y());
        $this->assertSame(-6.0, $v3->z());
        $this->assertSame(0.0, $v3->w());
    }

    public function test_can_be_negated(): void
    {
        $t1 = Tuple::create(1.0, -2.0, 3.0, -4.0);

        $t2 = $t1->negate();

        $this->assertSame(-1.0, $t2->x());
        $this->assertSame(2.0, $t2->y());
        $this->assertSame(-3.0, $t2->z());
        $this->assertSame(4.0, $t2->w());
    }

    public function test_can_be_multiplied_by_a_scalar(): void
    {
        $t1 = Tuple::create(1.0, -2.0, 3.0, -4.0);

        $t2 = $t1->multiplyBy(3.5);

        $this->assertSame(3.5, $t2->x());
        $this->assertSame(-7.0, $t2->y());
        $this->assertSame(10.5, $t2->z());
        $this->assertSame(-14.0, $t2->w());
    }
}
