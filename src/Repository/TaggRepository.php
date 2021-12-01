<?php

namespace App\Repository;

use App\Entity\Tagg;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tagg|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tagg|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tagg[]    findAll()
 * @method Tagg[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaggRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tagg::class);
    }

}
