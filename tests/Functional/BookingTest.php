<?php

namespace App\Tests\Functional;

use App\Factory\BookingFactory;
use App\Factory\CustomerFactory;
use App\Factory\TripFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Mailer\Test\InteractsWithMailer;

class BookingTest extends KernelTestCase
{
    use ResetDatabase, Factories, HasBrowser, InteractsWithMailer;

    /**
     * @test
     */
    public function testCreateBooking(): void
    {
        $trip = TripFactory::createOne([
            'name' => 'Visit ISS',
            'slug' => 'iss',
            'tagLine' => 'The International Space Station',
        ]);

        BookingFactory::assert()->empty();
        CustomerFactory::assert()->empty();

        $this->browser()
            ->throwExceptions()
            ->visit('/trip/iss')
            ->assertSuccessful()
            ->fillField('Name', 'Bruce Wayne')
            ->fillField('Email', 'bruce@wayne-enterprises.com')
            ->fillField('Travel Date', (new \DateTime('+1 month'))->format('Y-m-d'))
            ->clickAndIntercept('Book Trip')
            ->assertRedirectedTo('/booking/' . BookingFactory::first()->getUid())
            ->assertSuccessful()
            ->assertSeeIn('h1', 'Visit ISS')
            ->assertSee('The International Space Station')
        ;

        CustomerFactory::assert()
            ->count(1)
            ->exists(['name' => 'Bruce Wayne', 'email' => 'bruce@wayne-enterprises.com'])
        ;
        BookingFactory::assert()
            ->count(1)
            ->exists(['trip' => $trip, 'customer' => CustomerFactory::first()])
        ;

        // Test email functionality with zenstruck/mailer-test
        $this->mailer()
            ->assertSentEmailCount(1)
            ->assertEmailSentTo('bruce@wayne-enterprises.com', function ($email) {
                $email
                    ->assertSubject('Booking Confirmation for Visit ISS')
                    ->assertContains('Visit ISS')
                    ->assertContains('/booking/' . BookingFactory::first()->getUid())
                    ->assertHasFile('Terms of Service.pdf')
                ;
            })
        ;
    }
}
