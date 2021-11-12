<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use function Sentry\init;

class QuestionController extends AbstractController
{

    private $logger;
    private $isDebug;

    public function __construct(LoggerInterface $logger, bool $isDebug)
    {

        $this->logger = $logger;
        $this->isDebug = $isDebug;
    }
    /**
     * @Route("/", name="app_homepage")
     */
    //public function homepage(EntityManagerInterface $entityManager) // tutaj przez entity managera wczytujemy pozniej repozytorium.,
    public function homepage(QuestionRepository $repository) // tutaj przez entity managera wczytujemy pozniej repozytorium.,
    {

        //$repository = $entityManager->getRepository(Question::class);
        $questions = $repository->findAllAskedOrderByNewest();
        //$questions = $repository->findBy([], ['askedAt' => 'DESC']);


        //$html = $twigEnvironment->render('question/homepage.html.twig'); //użycie w taki sposó zwraca string z html
        //return new Response($html);

        return $this->render('question/homepage.html.twig', [
            'questions' => $questions,
        ]);
        //return new Response('Pierwszy tekst w aplikacji :) ');
    }

    /**
     * @Route("/questions/new")
     */
    public function new(EntityManagerInterface $entityManager)
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

        $entityManager->persist($question);
        $entityManager->flush();

        return new Response(sprintf(
            'Witam. Nowe zapytanie o id #%d, slug %s',
            $question->getId(),
            $question->getSlug(),
        ));
    }


    /**
     * Nowsza wersja funklcji show
     *
     * @Route("/questions/{slug}", name="app_question_show")
     *
     * skrócona wersja dzieki sensio framework extra bundle
     * bierze nazwe zmiennej z czesci Rouite, czyli SLUG i szyka takeij w encji
     *
     * jesli podamy slug, któego nie ma w bazie, albo po id to zwraca nam 404
     * to się nazywa param converter
     *
     * czasami jak bardziej skomplikowany przypadek to nie zadziałą.
     * Dla większości przypadkó podstawowych jest ok
     */
    public function show(Question $question)
    {

        if ($this->isDebug) {
            $this->logger->info('jesteśmy w trybie debug');
        }

        $answers = [
            'Make sure your cat is sitting',
            'Honestly, I like furry `shoes` better than my cat',
            'Maybe... Try saying the spell backwards',
        ];

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }


    /**
     * Poprzednia wersja funkcji z kursu, do poglądu
     * Stary czyli ta wersje jest lepsza.
     *
     * @Route("/questions/old/{slug}", name="app_question_show_old")
     */
    public function show_old($slug, MarkdownHelper $markdownHelper, EntityManagerInterface $entityManager)
    {

        if ($this->isDebug) {
            $this->logger->info('jesteśmy w trybie debug');
        }

        $repository = $entityManager->getRepository(Question::class);
        /** @var Question|null $question */
        $question = $repository->findOneBy(['slug' => $slug]);
        if (!$question) {
            throw $this->createNotFoundException(sprintf('Nie znaleziono pytania "%s"', $slug));
        }

        $answers = [
            'Make sure your cat is sitting',
            'Honestly, I like furry `shoes` better than my cat',
            'Maybe... Try saying the spell backwards',
        ];

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/questions/{slug}/vote", name="app_question_vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request, EntityManagerInterface $entityManager)
    {
        $direction = $request->request->get('direction');//pobiera element direction

        if ($direction === 'up') {
            $question->upVote();
        } elseif ($direction === 'down') {
            $question->downVote();
        }
        //inne ignorujemy

        $entityManager->flush();

        return $this->redirectToRoute('app_question_show', [
            'slug' => $question->getSlug(),
        ]);
    }
}