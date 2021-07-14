<?php
namespace App\Tests;

use App\Tests\Traits\NeedUser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    use NeedUser;

    public function testAuthticationSuccess(): void
    {
        $client = static::createClient();

        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        $crawler = $client->request("GET", $urlGenerator->generate("app_login"));

        $form = $crawler->selectButton("Sign in")->form([
            "email" => "user0@gmail.com",
            "password" => "password"
        ]);

        $client->submit($form);
 
        $this->assertResponseStatusCodeSame(302);
    }

    public function testUserPageAccessRedirect(): void
    {
        $client = static::createClient();
        $client->request('GET', '/user');

        $this->assertResponseRedirects("/login");
    }

    public function testUserPageAccessSuccess(): void
    {
        $client = static::createClient();
        $this->login($client);
        
        $client->request('GET', '/user');
        $this->assertResponseIsSuccessful();

        $client->request('GET', '/login');
        $this->assertResponseRedirects('/');
    }

    public function testAuthenticatError(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('Sign in')->form();
        $form['email'] = "aze@aze.com";
        $form['password'] = "azeaze";

        $client->submit($form);
        $this->assertResponseStatusCodeSame(302);
    }

}