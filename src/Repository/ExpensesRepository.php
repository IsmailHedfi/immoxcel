<?php

namespace App\Repository;

use App\Entity\Expenses;
use App\Model\SearchData;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @extends ServiceEntityRepository<Expenses>
 *
 * @method Expenses|null find($id, $lockMode = null, $lockVersion = null)
 * @method Expenses|null findOneBy(array $criteria, array $orderBy = null)
 * @method Expenses[]    findAll()
 * @method Expenses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExpensesRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Expenses::class);
    }

    public function findSalaryExpenseForCurrentMonth($capitalId): ?Expenses
    {
        $currentDate = new \DateTime();
        $startOfMonth = new \DateTime('first day of this month');
        $endOfMonth = new \DateTime('last day of this month');

        return $this->createQueryBuilder('e')
            ->leftJoin('e.capital', 'c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $capitalId)
            ->andWhere('e.Type = :type') // Adjusted to use lowercase "Type"
            ->setParameter('type', 'Salary')
            ->andWhere('e.dateE BETWEEN :startOfMonth AND :endOfMonth')
            ->setParameter('startOfMonth', $startOfMonth)
            ->setParameter('endOfMonth', $endOfMonth)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function findNotActiveTransactions()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.archived = :archived')
            ->setParameter('archived', false)
            ->getQuery()
            ->getResult();
    }
    public function findActiveTransactions()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.archived = :archived')
            ->setParameter('archived', true)
            ->getQuery()
            ->getResult();
    }
    public function orderByDest()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.archived = :archived')
            ->setParameter('archived', true)
            ->orderBy('s.id', 'DESC') // Assuming 'id' is the correct field name
            ->getQuery()
            ->getResult();
    }
    public function findExpensesBySearch($query, $type)
    {
        $queryBuilder = $this->createQueryBuilder('e')
            ->andWhere('e.Type = :type')
            ->setParameter('type', $type);

        if (!empty($query)) {
            $queryBuilder->andWhere('e.Description LIKE :query')
                ->setParameter('query', '%' . $query . '%');
        }

        return $queryBuilder->getQuery()->getResult();
    }



    public function Income()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.Type = :Income')
            ->setParameter('Income', 'Oui')
            ->getQuery()->getResult();
    }
    public function Salary()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.Type = :Salary')
            ->setParameter('Salary', 'Oui')
            ->getQuery()->getResult();
    }
    public function Expenses()
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.Type = :Expenses')
            ->setParameter('Expenses', 'Oui')
            ->getQuery()->getResult();
    }


    public function findbySearch(SearchData $searchData): array
    {
        $queryBuilder = $this->createQueryBuilder('p');

        if (!empty($searchData->q)) {
            $queryBuilder->andWhere('p.Description LIKE :description')
                ->orWhere('p.Type LIKE :q')
                ->orWhere('p.Totalamount LIKE :Totalamount')
                ->setParameter('Totalamount', '%' . $searchData->q . '%')
                ->setParameter('description', '%' . $searchData->q . '%')
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

    public function findEntitiesByString($str)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT p FROM pidevsymfony:expenses p WHERE p.type LIKE :str '
            )
            ->setParameter('str', '%' . $str . '%')
            ->getResult();
    }
    public function searchByInput($input)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.Type LIKE :input OR e.Description LIKE :input OR e.Totalamount LIKE :input OR e.Totalamount LIKE :input')
            ->setParameter('input', '%' . $input . '%')
            ->getQuery()
            ->getResult();
    }







    //    /**
    //     * @return Expenses[] Returns an array of Expenses objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Expenses
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
