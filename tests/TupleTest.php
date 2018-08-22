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
    public function testCanBePoint(): void
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
    public function testCanBeVector(): void
    {
        $a = Tuple::vector(4.3, -4.2, 3.1);

        $this->assertSame(4.3, $a->x());
        $this->assertSame(-4.2, $a->y());
        $this->assertSame(3.1, $a->z());
        $this->assertSame(0.0, $a->w());
        $this->assertFalse($a->isPoint());
        $this->assertTrue($a->isVector());
    }

    public function testAdditionOfPointAndVectorWorks(): void
    {
        $a = Tuple::point(3.0, -2.0, 5.0);
        $b = Tuple::vector(-2.0, 3.0, 1.0);

        $c = $a->plus($b);

        $this->assertSame(1.0, $c->x());
        $this->assertSame(1.0, $c->y());
        $this->assertSame(6.0, $c->z());
        $this->assertSame(1.0, $c->w());
    }

    public function testAdditionOfTwoVectorsWorks(): void
    {
        $a = Tuple::vector(3.0, -2.0, 5.0);
        $b = Tuple::vector(-2.0, 3.0, 1.0);

        $c = $a->plus($b);

        $this->assertSame(1.0, $c->x());
        $this->assertSame(1.0, $c->y());
        $this->assertSame(6.0, $c->z());
        $this->assertSame(0.0, $c->w());
    }

    public function testAdditionOfTwoPointsDoesNotWork(): void
    {
        $a = Tuple::point(3.0, -2.0, 5.0);

        $this->expectException(RuntimeException::class);

        $a->plus($a);
    }
}
