<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\ForAllExternal;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Property;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Intersection::class)]
#[CoversClass(PreparedComputation::class)]
#[UsesClass(Color::class)]
#[UsesClass(IntersectionCollection::class)]
#[UsesClass(IntersectionCollectionIterator::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(Ray::class)]
#[UsesClass(Shape::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(Transformations::class)]
#[UsesClass(Tuple::class)]
#[Group('intersection')]
#[Small]
final class IntersectionTest extends TestCase
{
    public function test_an_intersection_encapsulates_t_and_shape(): void
    {
        $t = 3.5;

        $s = Sphere::default();

        $i = Intersection::from($t, $s);

        $this->assertSame($t, $i->t());
        $this->assertSame($s, $i->shape());
    }

    public function test_the_hit_when_an_intersection_occurs_on_the_outside(): void
    {
        $r = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));
        $s = Sphere::default();
        $i = Intersection::from(4.0, $s);

        $comps = $i->prepare($r);

        $this->assertSame($comps->t(), $i->t());
        $this->assertSame($comps->shape(), $i->shape());
        $this->assertTrue($comps->point()->equalTo(Tuple::point(0, 0, -1)));
        $this->assertTrue($comps->eye()->equalTo(Tuple::vector(0, 0, -1)));
        $this->assertTrue($comps->normal()->equalTo(Tuple::vector(0, 0, -1)));
        $this->assertFalse($comps->inside());
    }

    public function test_the_hit_when_an_intersection_occurs_on_the_inside(): void
    {
        $r = Ray::from(Tuple::point(0, 0, 0), Tuple::vector(0, 0, 1));
        $s = Sphere::default();
        $i = Intersection::from(1.0, $s);

        $comps = $i->prepare($r);

        $this->assertSame($comps->t(), $i->t());
        $this->assertSame($comps->shape(), $i->shape());
        $this->assertTrue($comps->point()->equalTo(Tuple::point(0, 0, 1)));
        $this->assertTrue($comps->eye()->equalTo(Tuple::vector(0, 0, -1)));
        $this->assertTrue($comps->normal()->equalTo(Tuple::vector(0, 0, -1)));
        $this->assertTrue($comps->inside());
    }

    public function test_the_hit_should_offset_the_point(): void
    {
        $ray = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));

        $shape = Sphere::default();
        $shape->setTransform(Transformations::translation(0, 0, 1));

        $comps = Intersection::from(5, $shape)->prepare($ray);

        $this->assertLessThan(-0.00001 / 2, $comps->overPoint()->z);
        $this->assertGreaterThan($comps->overPoint()->z, $comps->point()->z);
    }

    #[Property]
    public function test_the_computations_for_an_intersection_are_prepared_for_an_eye_that_looks_against_the_direction_of_the_ray(
        #[ForAllExternal(RaytracerGenerators::class, 'raysThroughTheUnitSphere')]
        Ray $ray,
    ): void {
        $sphere = Sphere::default();

        foreach ($sphere->intersect($ray) as $intersection) {
            $comps = $intersection->prepare($ray);

            $this->assertSame($intersection->t(), $comps->t());
            $this->assertSame($sphere, $comps->shape());
            $this->assertEquals($ray->position($intersection->t()), $comps->point());
            $this->assertEquals($ray->direction()->negate(), $comps->eye());
            $this->assertEqualsWithDelta(1.0, $comps->normal()->magnitude(), 0.000000001);

            // The normal points to the side of the surface that the eye is on,
            // and so does the offset of the point that is used to avoid that
            // a surface casts a shadow on itself
            $this->assertGreaterThanOrEqual(0.0, $comps->normal()->dot($comps->eye()));
            $this->assertGreaterThan(0.0, $comps->overPoint()->minus($comps->point())->dot($comps->normal()));
            $this->assertEqualsWithDelta(0.00001, $comps->overPoint()->minus($comps->point())->magnitude(), 0.000000001);
        }
    }

    #[Property]
    public function test_a_ray_enters_a_sphere_from_the_outside_and_leaves_it_from_the_inside(
        #[ForAllExternal(RaytracerGenerators::class, 'raysThroughTheUnitSphere')]
        Ray $ray,
    ): void {
        $xs = Sphere::default()->intersect($ray);

        $this->assertCount(2, $xs);
        $this->assertFalse($xs->at(0)->prepare($ray)->inside());
        $this->assertTrue($xs->at(1)->prepare($ray)->inside());
    }

    /**
     * @param int<1, 1000> $distance
     */
    #[Property]
    public function test_a_ray_that_only_touches_a_sphere_is_not_inside_of_the_sphere(int $distance): void
    {
        $ray   = Ray::from(Tuple::point(0, 1, -$distance), Tuple::vector(0, 0, 1));
        $comps = Intersection::from($distance, Sphere::default())->prepare($ray);

        $this->assertFalse($comps->inside());
        $this->assertEquals(Tuple::vector(0, 1, 0), $comps->normal());
    }
}
