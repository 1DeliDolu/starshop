<?php

namespace App\Repository;

use App\Entity\Starship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Model\StarshipStatusEnum;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Adapter\ArrayAdapter;

/**
 * @extends ServiceEntityRepository<Starship>
 */
class StarshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Starship::class);
    }

    //    /**
    //     * @return Starship[] Returns an array of Starship objects
    //     */
    public function findIncompleteOrderedByDroidCount(): Pagerfanta
    {
        // Basit çözüm: Memory'de sıralama yapalım
        $query = $this->createQueryBuilder('s')
            ->where('s.status != :status')
            ->setParameter('status', StarshipStatusEnum::COMPLETED)
            ->getQuery();

        $starships = $query->getResult();

        // Her starship için droid sayısını hesaplayıp sıralayalım
        usort($starships, function ($a, $b) {
            $aDroidCount = $a->getStarshipDroids()->count();
            $bDroidCount = $b->getStarshipDroids()->count();
            return $aDroidCount <=> $bDroidCount;
        });

        // ArrayAdapter kullanarak paginator oluşturalım
        return new Pagerfanta(new ArrayAdapter($starships));
    }

    public function findMyShip(): ?Starship
    {
        $starships = $this->findAll();
        return $starships ? $starships[0] : null;
    }
}
