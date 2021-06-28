<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class World
{
    private ObjectCollection $objects;

    private ?PointLight $light = null;

    public static function default(): self
    {
        $light = PointLight::from(
            Tuple::point(-10, 10, -10),
            Color::from(1, 1, 1)
        );

        $s1 = new Sphere;
        $s1->setMaterial(Material::from(Color::from(0.8, 1.0, 0.6), 0.1, 0.7, 0.2, 200.0));

        $s2 = new Sphere;
        $s2->setTransformation(Transformations::scaling(0.5, 0.5, 0.5));

        $w = new self;

        $w->add($s1);
        $w->add($s2);
        $w->setLight($light);

        return $w;
    }

    public function __construct()
    {
        $this->objects = new ObjectCollection;
    }

    public function add(Object_ $object): void
    {
        $this->objects->add($object);
    }

    public function setObjects(ObjectCollection $objects): void
    {
        $this->objects = $objects;
    }

    public function objects(): ObjectCollection
    {
        return $this->objects;
    }

    /**
     * @throws WorldHasNoLightException
     */
    public function light(): PointLight
    {
        if ($this->light === null) {
            throw new WorldHasNoLightException;
        }

        return $this->light;
    }

    public function setLight(PointLight $light): void
    {
        $this->light = $light;
    }

    /**
     * @throws RuntimeException
     * @throws WorldHasNoLightException
     */
    public function colorAt(Ray $r): Color
    {
        $intersections = $this->intersect($r);

        if (!$intersections->hasHit()) {
            return Color::from(0, 0, 0);
        }

        /** @psalm-suppress MissingThrowsDocblock */
        $hit = $intersections->hit();

        return $this->shadeHit($hit->prepare($r));
    }

    public function intersect(Ray $r): IntersectionCollection
    {
        $intersections = IntersectionCollection::from();

        foreach ($this->objects as $object) {
            $intersections = $intersections->merge($object->intersect($r));
        }

        return $intersections;
    }

    /**
     * @throws RuntimeException
     * @throws WorldHasNoLightException
     */
    public function shadeHit(PreparedComputation $computation): Color
    {
        $shadowed = $this->isShadowed($computation->overPoint());

        return $computation->object()->material()->lighting(
            $this->light(),
            $computation->overPoint(),
            $computation->eye(),
            $computation->normal(),
            $shadowed
        );
    }

    /**
     * @throws RuntimeException
     * @throws WorldHasNoLightException
     */
    public function isShadowed(Tuple $point): bool
    {
        $v             = $this->light()->position()->minus($point);
        $distance      = $v->magnitude();
        $direction     = $v->normalize();
        $ray           = Ray::from($point, $direction);
        $intersections = $this->intersect($ray);

        return $intersections->hasHit() && $intersections->hit()->t() < $distance;
    }
}
