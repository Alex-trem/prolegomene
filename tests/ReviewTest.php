<?php

namespace App\Tests;

use App\Tests\Traits\NeedBooking;
use App\Tests\Traits\NeedUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReviewTest extends WebTestCase
{
    use NeedBooking;
    use NeedUser;

    public function testSendReviewSuccess()
    {
        $client = static::createClient();
        $this->login($client);
        $hotel = $this->createBooking($client);
        $crawler = $client->request('GET', "/booking/$hotel->slug");

        $form = $crawler->selectButton('Send')->form([
            "review_form[rating]" => 8,
            // 22 parce qu'il y a 20 fixtures dont le user n'est pas celui de ce test et le 21ème est créé dans BookingTest sur un autre hotel
            "review_form[booking]" => 22,
            "review_form[comment]" => "Test review",
        ]);

        $client->submit($form);

        $this->assertResponseRedirects("/booking/$hotel->slug");

    }
}