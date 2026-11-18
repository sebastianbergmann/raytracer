<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function iterator_to_array;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\PropertyBased\Generators;
use PHPUnit\Framework\TestCase;

#[CoversClass(ShapeCollection::class)]
#[CoversClass(ShapeCollectionIterator::class)]
#[UsesClass(Color::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Plane::class)]
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

    /**
     * @param int<0, 8> $numberOfShapes
     */
    #[Property]
    public function test_shapes_are_kept_in_the_order_in_which_they_were_added(int $numberOfShapes): void
    {
        $collection = new ShapeCollection;
        $shapes     = [];

        $this->assertTrue($collection->isEmpty());
        $this->assertFalse($collection->isNotEmpty());

        for ($i = 0; $i < $numberOfShapes; $i++) {
            $shapes[$i] = $i % 2 === 0 ? Sphere::default() : Plane::default();

            $collection->add($shapes[$i]);

            $this->assertCount($i + 1, $collection);
            $this->assertFalse($collection->isEmpty());
            $this->assertTrue($collection->isNotEmpty());
            $this->assertSame($shapes[$i], $collection->at($i));
        }

        $this->assertSame($shapes, $collection->asArray());
        $this->assertSame($shapes, iterator_to_array($collection));
    }

    /**
     * @param int<0, 8> $numberOfShapes
     */
    #[Property]
    public function test_there_is_no_shape_at_a_position_that_is_negative_or_not_less_than_the_number_of_shapes(int $numberOfShapes): void
    {
        $collection = new ShapeCollection;

        for ($i = 0; $i < $numberOfShapes; $i++) {
            $collection->add(Sphere::default());
        }

        $position = $this->draw(
            Generators::oneOf(
                Generators::integers(maximum: -1),
                Generators::integers(minimum: $numberOfShapes),
            ),
            'position',
        );

        $this->expectException(OutOfBoundsException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $collection->at($position);
    }
}
