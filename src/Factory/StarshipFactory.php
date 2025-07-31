<?php

namespace App\Factory;

use App\Entity\Starship;
use App\Model\StarshipStatusEnum;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Starship>
 */
final class StarshipFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    private const SHIP_NAMES = [
        'Nebula Drifter',
        'Quantum Voyager',
        'Starlight Nomad',
    ];

    public function __construct()
    {
    }

    public static function class(): string
    {
        return Starship::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->randomElement(self::SHIP_NAMES),
            'class' => self::faker()->randomElement(self::CLASSES),
            'captain' => self::faker()->randomElement(self::CAPTAINS),
            'status' => self::faker()->randomElement(\App\Model\StarshipStatusEnum::cases()),
            'arrivedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('-1 year', 'now')),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    private const CLASSES = [
        'Eclipse',
        'Vanguard',
        'Specter',
        // ... lines 51 - 57
    ];
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Starship $starship): void {})
        ;
    }
    private const CAPTAINS = [
        'Orion Stark',
        'Lyra Voss',
        'Cassian Drake',
        // ... lines 64 - 90
    ];
}
