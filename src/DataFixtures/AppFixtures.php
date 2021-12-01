<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
use App\Factory\AnswerFactory;
use App\Factory\ArticleFactory;
use App\Factory\ProductFactory;
use App\Factory\QuestionFactory;
use App\Factory\TagFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        TagFactory::createMany(100);
        //dla tych 20 Pytań chcemy powiazać z przypadkowymi Tagami

        ArticleFactory::createMany(20);

        $questions = QuestionFactory::createMany(20, function() {
            return [
                'tags' => TagFactory::randomRange(0, 5) # to zwraca zawsze jednen wynik. Random wykonywane jest tylko raz w tym przypadku
            ];
        }); //drugi parametr to ovveride (nadpisany)


        //drgui parasmetr to to co chcemy nadpisac.
        //QuestionFactory::createOne();
        QuestionFactory::new()
            ->unpublished()
            ->createMany(5);

        AnswerFactory::createMany(50, function () use ($questions){
            return [
                'question' => $questions[array_rand($questions)],
            ];
        });

        AnswerFactory::new(function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)],
            ];
        })->needsApproval()->many(20)->create();

        UserFactory::createOne([
            'firstName' => 'Leszek',
            'email' => 'leszek.leszek@gmail.com',
            'roles' => ['ROLE_ADMIN'],
        ]);

        UserFactory::createOne([
            'email' => 'abraca_user@example.com',
        ]);
        //pytanie - jak mamy w encji metode agreeToTerms() to jak jątutaj użyć?

        //UserFactory::createMany(10);

        ProductFactory::new()->createMany(20);

        $manager->flush();

        //tutaj ważne, żebyśmy najpierw stworzyli pytanbia a później ten kod z odpowiedziami.


        //$question = new Question();
        //$question->setName('Jak wykonać to zadanie?');
        //$question->setQuestion(' ... Nie powinienem tego robić....');

        //$answer->setQuestion($question);//nie dajemy ->getId()!!. wstawiamy cały obiekt.

        //ręczne dodawanie pytania i tagu
        //$question = QuestionFactory::createOne()->object();//lazy way using our factory - zwraca to proxy object+

        //nowe elementy


    }
}
