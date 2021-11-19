<?php

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Tag;
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

        //$answer->setQuestion($question);//nie dajemy ->getId()!!. wstawiamy cały obiekt.

        //ręczne dodawanie pytania i tagu
        $question = QuestionFactory::createOne()->object();//lazy way using our factory - zwraca to proxy object+


        //kiedy mieszasz ten FDoundary code ze zwykłym, zxasami dostajesz błąd.
        //aby temu zaradzić dodac trzeba ->object();
        $tag1 = new Tag();
        $tag1->setName('dinozaur');

        $tag2 = new Tag();
        $tag2->setName('monster trak');

        //$question->addTag($tag1);
        //$question->addTag($tag2);

        $tag1->addQuestion($question);
        $tag2->addQuestion($question);

        $manager->persist($tag1);
        $manager->persist($tag2);

        $manager->flush();

        $question->removeTag($tag1);//tak kasujemy - ale jak podać mu obiekt, któy kasujemy


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
