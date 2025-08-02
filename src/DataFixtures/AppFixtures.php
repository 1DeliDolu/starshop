<?php

namespace App\DataFixtures;

use App\Factory\StarshipFactory;
use App\Factory\DroidFactory;
use App\Entity\Droid;

use App\Entity\Starship;
use App\Model\StarshipStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        StarshipFactory::createOne([
            'name' => 'USS LeafyCruiser (NCC-0001)',
            'class' => 'Garden',
            'captain' => 'Jean-Luc Pickles',
            'status' => StarshipStatusEnum::IN_PROGRESS,
            'arrivedAt' => new \DateTimeImmutable('-1 day'),
        ]);
        StarshipFactory::createOne([
            'name' => 'USS Espresso (NCC-1234-C)',
            'class' => 'Latte',
            'captain' => 'James T. Quick!',
            'status' => StarshipStatusEnum::COMPLETED,
            'arrivedAt' => new \DateTimeImmutable('-1 week'),
        ]);
        StarshipFactory::createOne([
            'name' => 'USS Wanderlust (NCC-2024-W)',
            'class' => 'Delta Tourist',
            'captain' => 'Kathryn Journeyway',
            'status' => StarshipStatusEnum::WAITING,
            'arrivedAt' => new \DateTimeImmutable('-1 month'),
        ]);
        StarshipFactory::createMany(20);
        \App\Factory\StarshipPartFactory::createMany(100);
        DroidFactory::createMany(100);

        // Özel droidler ekleme
        $droid1 = new Droid();
        $droid1->setName('IHOP-123');
        $droid1->setPrimaryFunction('Pancake chef');
        $manager->persist($droid1);

        $droid2 = new Droid();
        $droid2->setName('D-3P0');
        $droid2->setPrimaryFunction('C-3PO\'s voice coach');
        $manager->persist($droid2);

        $droid3 = new Droid();
        $droid3->setName('BONK-5000');
        $droid3->setPrimaryFunction('Comedy sidekick');
        $manager->persist($droid3);

        // Özel bir starship oluşturup droidleri atayalım
        $starship = new Starship();
        $starship->setName('USS DroidCarrier (NCC-5000)');
        $starship->setClass('Droid Transport');
        $starship->setCaptain('Captain R2-D2');
        $starship->setStatus(StarshipStatusEnum::IN_PROGRESS);
        $starship->setArrivedAt(new \DateTimeImmutable('now'));

        // Droidleri starship'e atayalım
        $starship->addDroid($droid1);
        $starship->addDroid($droid2);
        $starship->addDroid($droid3);

        $manager->persist($starship);
        $manager->flush();

        // Doctrine'in büyüsü: Bir droid'i çıkaralım
        $starship->removeDroid($droid1);
        $manager->flush();
    }
}
