<?php

namespace App\Repository;

use App\Entity\Leaves;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Leaves>
 *
 * @method Leaves|null find($id, $lockMode = null, $lockVersion = null)
 * @method Leaves|null findOneBy(array $criteria, array $orderBy = null)
 * @method Leaves[]    findAll()
 * @method Leaves[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LeavesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Leaves::class);
    }

    public function getLeaveDataByYear(): array
{
    $entityManager = $this->getEntityManager();
    $query = $entityManager->createNativeQuery('
        SELECT MONTH(l.start_date) AS month, YEAR(l.start_date) AS year, COUNT(l.id) AS count
        FROM leaves l
        WHERE YEAR(l.start_date) >= YEAR(CURRENT_DATE())
        GROUP BY year, month
        ORDER BY year ASC, month ASC
    ', new ResultSetMapping());

    return $query->getResult();
}


//    /**
//     * @return Leaves[] Returns an array of Leaves objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Leaves
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
