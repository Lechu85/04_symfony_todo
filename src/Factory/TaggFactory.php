<?php

namespace App\Factory;

use App\Entity\Tagg;
use App\Repository\TaggRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Tagg>
 *
 * @method static Tagg|Proxy createOne(array $attributes = [])
 * @method static Tagg[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Tagg|Proxy find(object|array|mixed $criteria)
 * @method static Tagg|Proxy findOrCreate(array $attributes)
 * @method static Tagg|Proxy first(string $sortedField = 'id')
 * @method static Tagg|Proxy last(string $sortedField = 'id')
 * @method static Tagg|Proxy random(array $attributes = [])
 * @method static Tagg|Proxy randomOrCreate(array $attributes = [])
 * @method static Tagg[]|Proxy[] all()
 * @method static Tagg[]|Proxy[] findBy(array $attributes)
 * @method static Tagg[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Tagg[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static TaggRepository|RepositoryProxy repository()
 * @method Tagg|Proxy create(array|callable $attributes = [])
 */
final class TaggFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            // TODO add your default values here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories)
            'name' => self::faker()->text(),
            'slug' => self::faker()->text(),
            'createdAt' => null, // TODO add DATETIME ORM type manually
            'updatedAt' => null, // TODO add DATETIME ORM type manually
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Tagg $articleTag2) {})
        ;
    }

    protected static function getClass(): string
    {
        return Tagg::class;
    }
}
