<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use PHPUnit\Framework\TestCase;

/**
 * @covers \SebastianBergmann\Raytracer\Material
 *
 * @small
 */
final class MaterialTest extends TestCase
{
    public function test_the_default_material(): void
    {
        $m = Material::default();

        $this->assertSame(0.1, $m->ambient());
        $this->assertSame(0.9, $m->diffuse());
        $this->assertSame(0.9, $m->specular());
        $this->assertSame(200.0, $m->shininess());
    }
}
