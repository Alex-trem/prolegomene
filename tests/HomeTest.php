<?php
namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeTest extends WebTestCase
{
    public function testPagination(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertCount(9, $crawler->filter(".card"));
        $this->assertCount(1, $crawler->filter("a[data-role=next]"));
        $this->assertCount(0, $crawler->filter("a[data-role=previous]"));

        $crawler = $client->clickLink("Suivant");
        
        $this->assertResponseIsSuccessful();
        $this->assertEquals("page=2", substr($client->getRequest()->getUri(), -6, 6));
        $this->assertCount(9, $crawler->filter(".card"));
        $this->assertCount(1, $crawler->filter("a[data-role=next]"));
        $this->assertCount(1, $crawler->filter("a[data-role=previous]"));
        
        $crawler = $client->clickLink("Suivant");

        $this->assertResponseIsSuccessful();
        $this->assertEquals("page=3", substr($client->getRequest()->getUri(), -6, 6));
        $this->assertCount(2, $crawler->filter(".card"));
        $this->assertCount(0, $crawler->filter("a[data-role=next]"));
        $this->assertCount(1, $crawler->filter("a[data-role=previous]"));
    }
}