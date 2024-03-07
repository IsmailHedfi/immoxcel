<?php

namespace App\Repository;

use App\Entity\Supplier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\SearchData;

/**
 * @extends ServiceEntityRepository<Supplier>
 *
 * @method Supplier|null find($id, $lockMode = null, $lockVersion = null)
 * @method Supplier|null findOneBy(array $criteria, array $orderBy = null)
 * @method Supplier[]    findAll()
 * @method Supplier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SupplierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Supplier::class);
    }
    public function findbySearch(SearchData $searchData): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if (!empty($searchData->q)) {
            $queryBuilder->andWhere('p.Address LIKE :Address')
                ->orWhere('p.CompanyName LIKE :q')
                ->orWhere('p.PatentRef LIKE :PatentRef')
                ->setParameter('PatentRef', '%' . $searchData->q . '%')
                ->setParameter('Address', '%' . $searchData->q . '%')
                ->setParameter('q', '%' . $searchData->q . '%');
        }

        // Calculate the offset based on the page number
        $offset = ($searchData->page - 1) * 10; // Assuming 10 results per page

        // Set the max results and offset
        $queryBuilder->setMaxResults(10) // Assuming 10 results per page
            ->setFirstResult($offset);

        // Execute the query and return the results
        return $queryBuilder->getQuery()->getResult();
    }
    public function orderByDest()
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.id', 'DESC') // Assuming 'id' is the correct field name
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Supplier[] Returns an array of Supplier objects
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

    //    public function findOneBySomeField($value): ?Supplier
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
