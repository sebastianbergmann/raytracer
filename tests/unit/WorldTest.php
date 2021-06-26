<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\World
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Intersection
 * @uses \SebastianBergmann\Raytracer\IntersectionCollection
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\ObjectCollection
 * @uses \SebastianBergmann\Raytracer\ObjectCollectionIterator
 * @uses \SebastianBergmann\Raytracer\PointLight
 * @uses \SebastianBergmann\Raytracer\Ray
 * @uses \SebastianBergmann\Raytracer\Sphere
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class WorldTest extends TestCase
{
    public function test_creating_a_world(): void
    {
        $w = new World;

        $this->assertTrue($w->objects()->isEmpty());

        $this->expectException(WorldHasNoLightException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $w->light();
    }

    public function test_the_default_world(): void
    {
        $w = World::default();

        $this->assertCount(2, $w->objects());

        $this->assertTrue($w->objects()->at(0)->material()->color()->equalTo(Color::from(0.8, 1.0, 0.6)));
        $this->assertSame(0.1, $w->objects()->at(0)->material()->ambient());
        $this->assertSame(0.7, $w->objects()->at(0)->material()->diffuse());
        $this->assertSame(0.2, $w->objects()->at(0)->material()->specular());
        $this->assertSame(200.0, $w->objects()->at(0)->material()->shininess());

        $this->assertTrue($w->objects()->at(1)->material()->color()->equalTo(Color::from(1, 1, 1)));
        $this->assertSame(0.1, $w->objects()->at(1)->material()->ambient());
        $this->assertSame(0.9, $w->objects()->at(1)->material()->diffuse());
        $this->assertSame(0.9, $w->objects()->at(1)->material()->specular());
        $this->assertSame(200.0, $w->objects()->at(1)->material()->shininess());

        $this->assertTrue($w->light()->position()->equalTo(Tuple::point(-10, 10, -10)));
        $this->assertTrue($w->light()->intensity()->equalTo(Color::from(1, 1, 1)));
    }

    public function test_intersect_a_world_with_a_ray(): void
    {
        $w = World::default();
        $r = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));

        $xs = $w->intersect($r);

        $this->assertCount(4, $xs);

        $this->assertSame(4.0, $xs->at(0)->t());
        $this->assertSame(4.5, $xs->at(1)->t());
        $this->assertSame(5.5, $xs->at(2)->t());
        $this->assertSame(6.0, $xs->at(3)->t());
    }
}
