<?php

namespace App\Factory;

use App\Entity\ArticleReference;
use App\Repository\ArticleReferenceRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<ArticleReference>
 *
 * @method static ArticleReference|Proxy createOne(array $attributes = [])
 * @method static ArticleReference[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static ArticleReference|Proxy find(object|array|mixed $criteria)
 * @method static ArticleReference|Proxy findOrCreate(array $attributes)
 * @method static ArticleReference|Proxy first(string $sortedField = 'id')
 * @method static ArticleReference|Proxy last(string $sortedField = 'id')
 * @method static ArticleReference|Proxy random(array $attributes = [])
 * @method static ArticleReference|Proxy randomOrCreate(array $attributes = [])
 * @method static ArticleReference[]|Proxy[] all()
 * @method static ArticleReference[]|Proxy[] findBy(array $attributes)
 * @method static ArticleReference[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static ArticleReference[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ArticleReferenceRepository|RepositoryProxy repository()
 * @method ArticleReference|Proxy create(array|callable $attributes = [])
 */
final class ArticleReferenceFactory extends ModelFactory
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
            'filename' => self::faker()->text(),
            'originalFilename' => self::faker()->text(),
            'mimeType' => self::faker()->text(),
            'position' => self::faker()->randomNumber(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(ArticleReference $articleReference) {})
        ;
    }

    protected static function getClass(): string
    {
        return ArticleReference::class;
    }
}
