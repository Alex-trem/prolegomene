<?php
namespace App\Tests\Traits;

use App\Entity\User;
use App\Entity\Hotel;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;

trait NeedBooking
{
    public function createBooking($client)
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $client->getContainer()->get("doctrine.orm.entity_manager");

        /** @var Hotel $hotel */
        $hotel = $entityManager->getRepository(Hotel::class)->findOneBy(['id' => 6]);

        /** @var Booking $booking */
        $booking = (new Booking())
            ->setCustomers(1)
            ->setDepartureAt(new \DateTime())
            ->setDepartureAt((new \DateTimeImmutable())->modify("+1 day"))
            ->setHotel($hotel)
            ->setBedroomType(1)
            ->setUser($client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class)->findOneBy(["id" => 1]))
            ;
            
        $entityManager->persist($booking);
        $entityManager->flush();

        return $hotel;
    }
}