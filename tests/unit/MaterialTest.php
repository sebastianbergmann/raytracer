<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function sqrt;
use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Material
 *
 * @uses \SebastianBergmann\Raytracer\Color
 * @uses \SebastianBergmann\Raytracer\PointLight
 * @uses \SebastianBergmann\Raytracer\Tuple
 *
 * @small
 */
final class MaterialTest extends TestCase
{
    private Material $material;

    protected function setUp(): void
    {
        $this->material = Material::default();
    }

    public function test_the_default_material(): void
    {
        $this->assertTrue($this->material->color()->equalTo(Color::from(1, 1, 1)));
        $this->assertSame(0.1, $this->material->ambient());
        $this->assertSame(0.9, $this->material->diffuse());
        $this->assertSame(0.9, $this->material->specular());
        $this->assertSame(200.0, $this->material->shininess());
    }
}
