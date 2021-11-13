<?php

namespace App\DataFixtures;

use App\Entity\Question;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures_menual extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $question = new Question();
        $question->setName('Zgubione gacie :) ')
            ->setSlug('missing-pants-'.rand(0,1000))
            ->setQuestion(<<<EOF
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus nec quam sit amet 
nulla cursus vulputate. Suspendisse tristique auctor purus in commodo. Quisque non 
nisl sed quam fermentum porttitor. In dictum ligula at tempor gravida. Donec molestie 
imperdiet risus dignissim elementum. Curabitur nibh libero, pharetra ut magna vitae, 
vehicula tristique dui. Pellentesque pharetra non velit mattis ullamcorper. Maecenas 
quis urna faucibus lectus tristique semper sit amet eget ligula. Integer in iaculis odio.

Maecenas at ante ultricies, posuere magna et, iaculis magna. Ut finibus, eros et 
dignissim ullamcorper, arcu urna vehicula eros, sit amet tristique massa nunc id 
est. Sed rhoncus, mauris et congue blandit, elit ex dapibus sapien, quis semper 
lectus sem at ligula. Nullam bibendum risus ut tristique blandit. Morbi vitae felis 
lobortis, eleifend turpis quis, lacinia sem. Aenean justo enim, blandit ut gravida 
quis, tempus ac nunc. Sed ut justo vitae turpis vulputate posuere. Nam justo orci, 
auctor at tincidunt sit amet, ultrices ut risus. Morbi dictum nunc eu ex ornare, 
quis euismod erat luctus. Vivamus eget purus vitae erat viverra consequat eu in lacus.

EOF
            );

        if (rand(1, 10) > 2) {
            $question->setAskedAt(new \DateTime(sprintf('-%d days', rand(1, 100))));
        }

        $question->setVotes(rand(-20, 50));

        $manager->persist($question);
        $manager->flush();

    }
}
