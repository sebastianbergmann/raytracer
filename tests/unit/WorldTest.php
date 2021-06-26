<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\World
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\Material
 * @uses \SebastianBergmann\Raytracer\Matrix
 * @uses \SebastianBergmann\Raytracer\ObjectCollection
 * @uses \SebastianBergmann\Raytracer\PointLight
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
        $this->assertTrue($w->light()->intensity()->equalTo(Color::from(0.8, 1.0, 0.6)));
    }
}
