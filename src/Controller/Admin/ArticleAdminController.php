<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ArticleAdminController extends AbstractController
{
    //ROLE_ADMIN_ARTICLE
    /**
     * @Route("/admin/article/new", name="admin_article_new")
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(EntityManagerInterface $entityManager, Request $request)
    {

        //info drugi argument służy do wypełnienia danych w polach formuylarzy , ...
        $form = $this->createForm(ArticleFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {

            /** @var Article $article */
            $article = $form->getData();
            //$article->setTitle($data['title']);
            //$article->setContent($data['content']);
            //$article->setAuthor($this->getUser());

            //info nie musimy podawać każdego pola osobno, bo mechanizm formów to robi
            //info chyba m,ze chcemy to moemy tuitaj jakieś pola dodatkowo dodać.
            $entityManager->persist($article);
            $entityManager->flush($article);

            $this->addFlash('success', 'Artykuł został dodany.');

            return $this->redirectToRoute('app_homepage');

        }

        return $this->render('article_admin/new.html.twig',[
            'articleForm' => $form->createView(), //wstawiamy obiekt bezposrednio do twiga
        ]);
    }


    /**
     * @Route("/admin/article", name="admin_article_list")
     */
    public function list(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAll();

        return $this->render('article_admin/list.html.twig', [
            'articles' => $articles,
        ]);
    }


    /**
     * @Route("/admin/article/{id}/edit", name="admin_article_edit")
     * @IsGranted("ROLE_ADMIN", subject="article")
     */
    public function edit(Article $article, Request $request, EntityManagerInterface $entityManager)
    {


        $form = $this->createForm(ArticleFormType::class, $article, [
            'include_published_at' => true,
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {


            //info nie musimy podawać każdego pola osobno, bo mechanizm formów to robi
            //info chyba m,ze chcemy to moemy tuitaj jakieś pola dodatkowo dodać.
            $entityManager->persist($article);
            $entityManager->flush($article);

            $this->addFlash('success', 'Artykuł został uaktualniony.');

            return $this->redirectToRoute('admin_article_edit', [
                'id' => $article->getId(),
            ]);

        }

        return $this->render('article_admin/edit.html.twig',[
            'articleForm' => $form->createView(), //wstawiamy obiekt bezposrednio do twiga
        ]);
    }
}
