<?php

namespace App\Factory;

use App\Entity\ApiToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<User>
 *
 * @method static User|Proxy createOne(array $attributes = [])
 * @method static User[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static User|Proxy find(object|array|mixed $criteria)
 * @method static User|Proxy findOrCreate(array $attributes)
 * @method static User|Proxy first(string $sortedField = 'id')
 * @method static User|Proxy last(string $sortedField = 'id')
 * @method static User|Proxy random(array $attributes = [])
 * @method static User|Proxy randomOrCreate(array $attributes = [])
 * @method static User[]|Proxy[] all()
 * @method static User[]|Proxy[] findBy(array $attributes)
 * @method static User[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static User[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static UserRepository|RepositoryProxy repository()
 * @method User|Proxy create(array|callable $attributes = [])
 */
final class UserFactory extends ModelFactory
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    protected function getDefaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'firstName' => self::faker()->firstName(),
            'plainPassword' => 'tada',
            'isVerified' => true,

        ];
    }

    //info wykonywana ta metodfa jest po zakonczeniu wczensiejszej
    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
             ->afterInstantiate(function(User $user) {

                 $apiToken = new ApiToken($user);
                 $this->entityManager->persist($apiToken);


                 //jesli has??o istnieje to je kodujwemy
                 if ($user->getPlainPassword()) {
                     $user->setPassword(
                         $this->passwordHasher->hashPassword($user, $user->getPlainPassword())
                     );
                 }
                 $user->agreeToTerms();

                 //PYTANIE - Jak tutaj wstrzyknac objectManager $manager aby zrobi?? persist? w metodzie, czy w kontruktorze? Nie dzia??a??o.
                 //NOTE Mia??em problem ,??eby ddoa?? apitoken factory, zdecydowea??em dodawa?? useroi r??cznie, nie z automtu przez fixtures.


                  $user->addApiToken($apiToken);
             });
        ;
    }

    protected static function getClass(): string
    {
        return User::class;
    }
}
