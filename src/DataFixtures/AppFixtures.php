<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Factory\AnswerFactory;
use App\Factory\QuestionFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //QuestionFactory::new()->create();
        $questions = QuestionFactory::createMany(20);
        //QuestionFactory::createOne();
        QuestionFactory::new()
            ->unpublished()
            ->createMany(5);

        AnswerFactory::createMany(100, function () use ($questions){
            return [
                'question' => $questions[array_rand($questions)],
            ];
        });

        AnswerFactory::new(function() use ($questions) {
            return [
                'question' => $questions[array_rand($questions)],
            ];
        })->needsApproval()->many(20)->create();

        //tutaj ważne, żebyśmy najpierw stworzyli pytanbia a później ten kod z odpowiedziami.



        //$question = new Question();
        //$question->setName('Jak wykonać to zadanie?');
        //$question->setQuestion(' ... Nie powinienem tego robić....');

       // $answer->setQuestion($question);//nie dajemy ->getId()!!. wstawiamy cały obiekt.


        $manager->flush();

        //inne ręczna metoda dodania do bazy danych w relacji
/*
        $question = QuestionFactory::createOne();
        $answer1 = new Answer();
        $answer1->setContent('Przykładowa odpowiedź. Testuujemy mechanizmy. Przykładowa odpowiedź. Testuujemy mechanizmy. ');
        $answer1->setUsername('Artur');

        $answer2 = new Answer();
        $answer2->setContent('Przykładowa odpowiedź. Testuujemy mechanizmy. Przykładowa odpowiedź. Testuujemy mechanizmy. ');
        $answer2->setUsername('Tomek');

        $question->addAnswer($answer1);
        $question->addAnswer($answer2);

        $manager->persist($answer1);
        $manager->persist($answer2);

        $manager->flush();
*/
    }
}
