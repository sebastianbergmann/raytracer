<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Intersection
 * @covers \SebastianBergmann\Raytracer\PreparedComputation
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\Ray
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class IntersectionTest extends TestCase
{
    public function test_an_intersection_encapsulates_t_and_object(): void
    {
        $t = 3.5;

        $s = new Sphere;

        $i = Intersection::from($t, $s);

        $this->assertSame($t, $i->t());
        $this->assertSame($s, $i->object());
    }

    public function test_the_hit_when_an_intersection_occurs_on_the_outside(): void
    {
        $r = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));
        $s = new Sphere;
        $i = Intersection::from(4.0, $s);

        $comps = $i->prepare($r);

        $this->assertSame($comps->t(), $i->t());
        $this->assertSame($comps->object(), $i->object());
        $this->assertTrue($comps->point()->equalTo(Tuple::point(0, 0, -1)));
        $this->assertTrue($comps->eye()->equalTo(Tuple::vector(0, 0, -1)));
        $this->assertTrue($comps->normal()->equalTo(Tuple::vector(0, 0, -1)));
        $this->assertFalse($comps->inside());
    }

    public function test_the_hit_when_an_intersection_occurs_on_the_inside(): void
    {
        $r = Ray::from(Tuple::point(0, 0, 0), Tuple::vector(0, 0, 1));
        $s = new Sphere;
        $i = Intersection::from(1.0, $s);

        $comps = $i->prepare($r);

        $this->assertSame($comps->t(), $i->t());
        $this->assertSame($comps->object(), $i->object());
        $this->assertTrue($comps->point()->equalTo(Tuple::point(0, 0, 1)));
        $this->assertTrue($comps->eye()->equalTo(Tuple::vector(0, 0, -1)));
        $this->assertTrue($comps->normal()->equalTo(Tuple::vector(0, 0, -1)));
        $this->assertTrue($comps->inside());
    }

    public function test_the_hit_should_offset_the_point(): void
    {
        $ray = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));

        $shape = new Sphere;
        $shape->setTransformation(Transformations::translation(0, 0, 1));

        $comps = Intersection::from(5, $shape)->prepare($ray);

        $this->assertLessThan(-Intersection::EPSILON / 2, $comps->overPoint()->z());
        $this->assertGreaterThan($comps->overPoint()->z(), $comps->point()->z());
    }
}
