<?php
namespace App\Tests\Traits;

use App\Entity\User;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait NeedUser
{
    public function login($client)
    {
        $session = $client->getContainer()->get('session');
        /** @var User $user */
        $user = $client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->findOneBy(["id" => 1]);
        $token = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();
        return $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    }
}