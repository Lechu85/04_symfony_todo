<?php

namespace App\DataFixtures;

use App\Entity\Question;
use App\Factory\QuestionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //QuestionFactory::new()->create();
        QuestionFactory::createMany(20);
        //QuestionFactory::createOne();
        QuestionFactory::new()
            ->unpublished()
            ->createMany(5);


    }
}
