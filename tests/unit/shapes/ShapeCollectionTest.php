<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShapeCollection::class)]
#[CoversClass(ShapeCollectionIterator::class)]
#[UsesClass(Color::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(Tuple::class)]
#[Group('shape')]
#[Small]
final class ShapeCollectionTest extends TestCase
{
    public function test_aggregating_objects(): void
    {
        $s1 = Sphere::default();
        $s2 = Sphere::default();

        $objects = new ShapeCollection;
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
