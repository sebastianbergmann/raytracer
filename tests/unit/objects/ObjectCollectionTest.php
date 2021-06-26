<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\ObjectCollection
 * @covers \SebastianBergmann\Raytracer\ObjectCollectionIterator
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class ObjectCollectionTest extends TestCase
{
    public function test_aggregating_objects(): void
    {
        $s1 = new Sphere;
        $s2 = new Sphere;

        $objects = new ObjectCollection;
        $objects->add($s1);
        $objects->add($s2);

        $this->assertCount(2, $objects);
        $this->assertTrue($objects->isNotEmpty());
        $this->assertFalse($objects->isEmpty());

        $this->assertSame($s1, $objects->at(0));
        $this->assertSame($s2, $objects->at(1));

        $number = 0;

        foreach ($objects as $i => $object) {
            $this->assertSame($number, $i);

            if ($i === 0) {
                $this->assertSame($s1, $object);
            } else {
                $this->assertSame($s2, $object);
            }

            $number++;
        }

        $this->expectException(OutOfBoundsException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $objects->at(2);
    }
}
