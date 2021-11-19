<?php

namespace App\Repository;

use App\Entity\Answer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Answer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Answer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Answer[]    findAll()
 * @method Answer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Answer::class);
    }

    public static function createApprovedCriteria(): Criteria
    {
        return Criteria::create()
            ->andWhere(Criteria::expr()->eq('status', Answer::STATUS_APPROVED));

    }

    /**
     * @return Answer[]
     */
    public function findAllApproved(int $max = 10): array
    {
        //criteria używamny do zmniejhszenia obciążenia bazy.
        //pobieramy, ale chcemy użyć już logike powyższą, wyboru approved opcji.
        //dodajemy addCriteria()
        return $this->createQueryBuilder('answer')
            ->addCriteria(self::createApprovedCriteria())
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Answer[]
     *
     * czyli zwraca tablice z Answer obiect
     */
    public function findMostPopular(string $search = null): array
    {
        $queryBuilder =  $this->createQueryBuilder('answer')
            ->addCriteria(self::createApprovedCriteria())
            ->orderBy('answer.votes', 'DESC')
            ->innerJoin('answer.question', 'question')
            ->addSelect('question');

        //opcjonalnmy parametr dodajemy
        if ($search) {
            $queryBuilder->andWhere('answer.content LIKE :searchTerm or question.question LIKE :searchTerm')//:searchTerm to placeholder
                ->setParameter('searchTerm', '%'.$search.'%'); //% to fazi search

        }

        return $queryBuilder
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

//         Podobne, ale w jednym kawałku, wyżej podzieliłchłop na czesci.
//        return $this->createQueryBuilder('answer')
//            ->addCriteria(self::createApprovedCriteria())
//            ->orderBy('answer.votes', 'DESC')
//            ->innerJoin('answer.question', 'question')
//            ->addSelect('question')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult();

    }
    // /**
    //  * @return Answer[] Returns an array of Answer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Answer
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
