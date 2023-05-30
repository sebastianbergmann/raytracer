<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(World::class)]
#[UsesClass(Color::class)]
#[UsesClass(Intersection::class)]
#[UsesClass(IntersectionCollection::class)]
#[UsesClass(Material::class)]
#[UsesClass(Matrix::class)]
#[UsesClass(PointLight::class)]
#[UsesClass(PreparedComputation::class)]
#[UsesClass(Ray::class)]
#[UsesClass(Shape::class)]
#[UsesClass(ShapeCollection::class)]
#[UsesClass(ShapeCollectionIterator::class)]
#[UsesClass(Sphere::class)]
#[UsesClass(Transformations::class)]
#[UsesClass(Tuple::class)]
#[Small]
final class WorldTest extends TestCase
{
    public function test_creating_a_world(): void
    {
        $w = new World;

        $this->assertTrue($w->shapes()->isEmpty());

        $this->expectException(WorldHasNoLightException::class);

        /* @noinspection UnusedFunctionResultInspection */
        $w->light();
    }

    public function test_the_default_world(): void
    {
        $w = World::default();

        $this->assertCount(2, $w->shapes());

        $this->assertTrue($w->shapes()->at(0)->material()->color()->equalTo(Color::from(0.8, 1.0, 0.6)));
        $this->assertSame(0.1, $w->shapes()->at(0)->material()->ambient());
        $this->assertSame(0.7, $w->shapes()->at(0)->material()->diffuse());
        $this->assertSame(0.2, $w->shapes()->at(0)->material()->specular());
        $this->assertSame(200.0, $w->shapes()->at(0)->material()->shininess());

        $this->assertTrue($w->shapes()->at(1)->material()->color()->equalTo(Color::from(1, 1, 1)));
        $this->assertSame(0.1, $w->shapes()->at(1)->material()->ambient());
        $this->assertSame(0.9, $w->shapes()->at(1)->material()->diffuse());
        $this->assertSame(0.9, $w->shapes()->at(1)->material()->specular());
        $this->assertSame(200.0, $w->shapes()->at(1)->material()->shininess());

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

    public function test_shading_an_intersection(): void
    {
        $w = World::default();
        $r = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));
        $s = $w->shapes()->at(0);

        $c = $w->shadeHit(Intersection::from(4.0, $s)->prepare($r));

        $this->assertTrue($c->equalTo(Color::from(0.38066, 0.47583, 0.2855)));
    }

    public function test_shading_an_intersection_from_the_inside(): void
    {
        $w = World::default();
        $w->setLight(
            PointLight::from(
                Tuple::point(0, 0.25, 0),
                Color::from(1, 1, 1),
            ),
        );

        $r = Ray::from(Tuple::point(0, 0, 0), Tuple::vector(0, 0, 1));
        $s = $w->shapes()->at(1);

        $c = $w->shadeHit(Intersection::from(0.5, $s)->prepare($r));

        $this->assertTrue($c->equalTo(Color::from(0.90498, 0.90498, 0.90498)));
    }

    public function test_the_color_when_a_ray_misses(): void
    {
        $w = World::default();
        $r = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 1, 0));

        $c = $w->colorAt($r);

        $this->assertTrue($c->equalTo(Color::from(0, 0, 0)));
    }

    public function test_the_color_when_a_ray_hits(): void
    {
        $w = World::default();
        $r = Ray::from(Tuple::point(0, 0, -5), Tuple::vector(0, 0, 1));

        $c = $w->colorAt($r);

        $this->assertTrue($c->equalTo(Color::from(0.38066, 0.47583, 0.2855)));
    }

    public function test_the_color_with_an_intersection_behind_the_ray(): void
    {
        $w = World::default();
        $r = Ray::from(Tuple::point(0, 0, 0.75), Tuple::vector(0, 0, -1));

        $outer = $w->shapes()->at(0);
        $outer->material()->setAmbient(1);

        $inner = $w->shapes()->at(1);
        $inner->material()->setAmbient(1);

        $c = $w->colorAt($r);

        $this->assertTrue($c->equalTo($inner->material()->color()));
    }

    public function test_there_is_no_shadow_when_nothing_is_collinear_with_the_point_and_light(): void
    {
        $w = World::default();
        $p = Tuple::point(0, 10, 0);

        $this->assertFalse($w->isShadowed($p));
    }

    public function test_the_shadow_when_an_object_is_between_the_point_and_the_light(): void
    {
        $w = World::default();
        $p = Tuple::point(10, -10, 10);

        $this->assertTrue($w->isShadowed($p));
    }

    public function test_there_is_no_shadow_when_an_object_is_behind_the_light(): void
    {
        $w = World::default();
        $p = Tuple::point(-20, 20, -20);

        $this->assertFalse($w->isShadowed($p));
    }

    public function test_there_is_no_shadow_when_an_object_is_behind_the_point(): void
    {
        $w = World::default();
        $p = Tuple::point(-2, -2, -2);

        $this->assertFalse($w->isShadowed($p));
    }

    #[TestDox('shadeHit() is given an intersection in shadow')]
    public function test_shadeHit_is_given_an_intersection_in_shadow(): void
    {
        $w = new World;
        $w->setLight(PointLight::from(Tuple::point(0, 0, -10), Color::from(1, 1, 1)));

        $s1 = Sphere::default();
        $w->add($s1);

        $s2 = Sphere::default();
        $s2->setTransform(Transformations::translation(0, 0, 10));
        $w->add($s2);

        $ray   = Ray::from(Tuple::point(0, 0, 5), Tuple::vector(0, 0, 1));
        $comps = Intersection::from(4, $s2)->prepare($ray);

        $c = $w->shadeHit($comps);

        $this->assertTrue($c->equalTo(Color::from(0.1, 0.1, 0.1)));
    }
}
