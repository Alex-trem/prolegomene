<?php

namespace App\Tests;

use DateTime;
use Generator;
use App\Entity\User;
use App\Entity\Hotel;
use App\Entity\Booking;
use App\Tests\Traits\NeedUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookingTest extends WebTestCase
{
    use NeedUser;

     public function testEntity(): void
     {
         $user = new User();
         $hotel = new Hotel();
         $departure = new DateTime("2020-06-10");
         $arrival = new DateTime("2020-06-11");
         $booking = (new Booking())
             ->setBedroomType('Single Room')
             ->setCustomers(2)
             ->setDepartureAt($departure)
             ->setArrivalAt($arrival)
             ->setUser($user)
             ->setHotel($hotel);

         $this->assertTrue($booking->getBedroomType() === "Single Room");
         $this->assertTrue($booking->getCustomers() === 2);
         $this->assertTrue($booking->getDepartureAt() === $departure);
         $this->assertTrue($booking->getArrivalAt() === $arrival);
         $this->assertTrue($booking->getUser() === $user);
         $this->assertTrue($booking->getHotel() === $hotel);
     }

     public function testHome(): void
     {
         $client = static::createClient();
         $client->request('GET', '/');

         $this->assertResponseIsSuccessful();
     }

     public function testBookingView(): void 
     {
         $client = static::createClient();

         $hotel = $client->getContainer()->get("doctrine.orm.entity_manager")->getRepository(Hotel::class)->findOneBy(['id' => 5]);
        
         $client->request('GET', "/booking/$hotel->slug");

         $this->assertResponseIsSuccessful();
         $this->assertSelectorTextContains('h4', 'Description');
    }

     public function testBookingSubmissionRedirectToLogin(): void
     {
         $client = static::createClient();
        
         $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");
         $hotel = $entityManager->getRepository(Hotel::class)->findOneBy(['id' => 5]);
        
         $crawler = $client->request('GET', "/booking/$hotel->slug");

         $form = $crawler->selectButton('Reserve')->form();
         $form['booking_form[customers]'] = "2";
         $form['booking_form[bedroomType]'] = "2";
         $form['booking_form[arrivalAt][year]'] =  "2021";
         $form['booking_form[arrivalAt][month]'] = "11";
         $form['booking_form[arrivalAt][day]'] = "11";
         $form['booking_form[departureAt][year]'] = "2021";
         $form['booking_form[departureAt][month]'] = "11";
         $form['booking_form[departureAt][day]'] = "12";

         $client->submit($form);

         $this->assertResponseRedirects("/login");
     }
     
          
     /**
      * testBookingSubmissionSuccess
      * @dataProvider dataProvider
      * @param array $formData
      * @param string $message
      * @return void
      */
     public function testBookingSubmissionSuccess(array $formData, string $message): void
     {
         $client = static::createClient();
         $this->login($client);
        
         $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");
         /** @var Hotel $hotel */
         $hotel = $entityManager->getRepository(Hotel::class)->findOneBy(['id' => 5]);
        
         $crawler = $client->request('GET', "/booking/$hotel->slug");

         $form = $crawler->selectButton('Reserve')->form($formData);
         

         $crawler = $client->submit($form);

         $this->assertResponseRedirects("/booking/$hotel->slug");
         $client->followRedirect();
         $this->assertSelectorTextContains("html", $message);
     }
    
     
     /**
      * provideData
      *
      * @return Generator
      */
     public function dataProvider(): Generator
     {
         yield [
            [
                'booking_form[customers]' => "2",
                'booking_form[bedroomType]' => "2",
                'booking_form[arrivalAt][year]' =>  "2026",
                'booking_form[arrivalAt][month]' => "11",
                'booking_form[arrivalAt][day]' => "11",
                'booking_form[departureAt][year]' => "2026",
                'booking_form[departureAt][month]' => "11",
                'booking_form[departureAt][day]' => "12",
            ],
            "Your booking has been registered"
        ];
        
        yield [
            [
                'booking_form[customers]' => "2",
                'booking_form[bedroomType]' => "2",
                'booking_form[arrivalAt][year]' =>  "2026",
                'booking_form[arrivalAt][month]' => "11",
                'booking_form[arrivalAt][day]' => "11",
                'booking_form[departureAt][year]' => "2026",
                'booking_form[departureAt][month]' => "11",
                'booking_form[departureAt][day]' => "12",
            ],
            "This room is no longer available for these dates"
        ];
        
        yield [
            [
                'booking_form[customers]' => "2",
                'booking_form[bedroomType]' => "2",
                'booking_form[arrivalAt][year]' =>  "2019",
                'booking_form[arrivalAt][month]' => "11",
                'booking_form[arrivalAt][day]' => "11",
                'booking_form[departureAt][year]' => "2019",
                'booking_form[departureAt][month]' => "11",
                'booking_form[departureAt][day]' => "12",
            ],
            "The date can't be in the past"
        ];
     }
    
}
