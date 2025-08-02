<?php

namespace App\Repository;

use App\Entity\StarshipPart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\Criteria;

/**
 * @extends ServiceEntityRepository<StarshipPart>
 */
class StarshipPartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StarshipPart::class);
    }

    public static function createExpensiveCriteria(): Criteria
    {
        return Criteria::create()->andWhere(Criteria::expr()->gt('price', 50000));
    }

    /**
     * @return StarshipPart[]
     */
    public function getExpensiveParts(int $limit = 10): array
    {
        return $this->createQueryBuilder('sp')
            ->addCriteria(self::createExpensiveCriteria())
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return StarshipPart[] Returns an array of StarshipPart objects ordered by price descending
     */
    public function findAllOrderedByPrice(string $search = ''): array
    {
        $qb = $this->createQueryBuilder('sp')
            ->orderBy('sp.price', 'DESC')
            ->innerJoin('sp.starship', 's')
            ->addSelect('s')
        ;
        if ($search) {
            $qb->andWhere('LOWER(sp.name) LIKE :search OR LOWER(sp.notes) LIKE :search')
                ->setParameter('search', '%' . strtolower($search) . '%');
        }
        return $qb->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return StarshipPart[] Returns an array of StarshipPart objects
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

    //    public function findOneBySomeField($value): ?StarshipPart
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
