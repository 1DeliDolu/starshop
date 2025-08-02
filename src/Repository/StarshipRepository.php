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
        // Basit yaklaşım: tüm starship'leri alıp PHP'de sıralayalım
        $starships = $this->createQueryBuilder('s')
            ->where('s.status != :status')
            ->setParameter('status', StarshipStatusEnum::COMPLETED)
            ->getQuery()
            ->getResult();

        // PHP'de droid sayısına göre sıralama
        usort($starships, function ($a, $b) {
            return count($a->getStarshipDroids()) <=> count($b->getStarshipDroids());
        });

        return new Pagerfanta(new ArrayAdapter($starships));
    }

    public function findMyShip(): ?Starship
    {
        $starships = $this->findAll();
        return $starships ? $starships[0] : null;
    }
}
