<?php

namespace App\Repository;

use App\Entity\SellPurchase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SellPurchase>
 *
 * @method SellPurchase|null find($id, $lockMode = null, $lockVersion = null)
 * @method SellPurchase|null findOneBy(array $criteria, array $orderBy = null)
 * @method SellPurchase[]    findAll()
 * @method SellPurchase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SellPurchaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SellPurchase::class);
    }

//    /**
//     * @return SellPurchase[] Returns an array of SellPurchase objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SellPurchase
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
