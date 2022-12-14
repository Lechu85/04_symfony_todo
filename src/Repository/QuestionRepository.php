<?php

namespace App\Repository;

use App\Entity\Question;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Question|null find($id, $lockMode = null, $lockVersion = null)
 * @method Question|null findOneBy(array $criteria, array $orderBy = null)
 * @method Question[]    findAll()
 * @method Question[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class QuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Question::class);
    }


    public function createAskedOrderByNewestQueryBuilder(): QueryBuilder
    {
        return $this->addIsAskedQueryBuilder()
            ->orderBy('q.askedAt','DESC')
            ->leftJoin('q.tags', 'tag') //left ponieważ chcemy dużo tagów dla pytania
            ->addSelect('tag')
        ;//joinig many to one wygląda tak samo jak joinig many to many

    }


    /**
    * @return Question[] Returns an array of Question objects
    */
    /*
     * Wersja poprzednia bez paginatora. Może się przydać do wyświetlenia poprostu np 10 pozycji.
    public function findAllAskedOrderByNewest()
    {
        $qb =  $this->createQueryBuilder('q');

        return $this->addIsAskedQueryBuilder($qb)
            ->orderBy('q.askedAt','DESC')
            ->leftJoin('q.tags', 'tag') //left ponieważ chcemy dużo tagów dla pytania
            ->addSelect('tag')
            ->getQuery()
            ->getResult()
        ;//joinig many to one wygląda tak samo jak joinig many to many

        //to zapytanie generuje błąd n+1 problem dla pobioerania tagów, wyżej rozwiązanie
//        return $this->addIsAskedQueryBuilder($qb)
//            ->orderBy('q.askedAt','DESC')
//            ->getQuery()
//            ->getResult()
//            //->getOneOrNullResult()
//        ;
    }*/

    private function addIsAskedQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $this->getOrCreateQueryBuilder($qb)
            ->andWhere('q.askedAt IS NOT NULL');
    }

    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('q');
    }

    /*
    public function findOneBySomeField($value): ?Question
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
