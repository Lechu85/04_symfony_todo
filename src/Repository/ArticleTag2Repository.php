<?php

namespace App\Repository;

use App\Entity\ArticleTag2;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ArticleTag2|null find($id, $lockMode = null, $lockVersion = null)
 * @method ArticleTag2|null findOneBy(array $criteria, array $orderBy = null)
 * @method ArticleTag2[]    findAll()
 * @method ArticleTag2[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleTag2Repository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArticleTag2::class);
    }

}
