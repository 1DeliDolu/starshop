<?php

namespace App\Repository;

use App\Model\Starship;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class StarshipRepository
{
    // This class is responsible for managing starship data.
    // It interacts with the database to fetch, save, and manipulate starship records.
    public function __construct(private LoggerInterface $logger)
    {
        // Constructor can be used to inject dependencies if needed.
    }

    public function findAll()
    {
        $this->logger->info('Fetching all starships from the repository');
        return [
            new Starship(1, 'Millennium Falcon', 'YT-1300', 'Corellian Engineering Corporation', 'Freighter', 'Han Solo', 'Active'),
            new Starship(2, 'X-wing', 'T-65 X-wing', 'Incom Corporation', 'Starfighter', 'Luke Skywalker', 'Active'),
            new Starship(3, 'Star Destroyer', 'Imperial I-class', 'Kuat Drive Yards', 'Capital Ship', 'Darth Vader', 'Destroyed'),
        ];
        // Logic to fetch all starships from the database.
    }

    public function findById($id)
    {
        // Logic to find a starship by its ID.
        // This could involve querying the database and returning a single record.
    }

}
