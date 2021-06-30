<?php

namespace App\DataFixtures;

use App\Entity\Bedroom;
use DateInterval;
use App\Entity\User;
use App\Entity\Hotel;
use App\Entity\Booking;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();
        
        for($i = 0; $i < 20; $i++) {
            $bedroom = (new Bedroom())->setType($i);
            $manager->persist($bedroom);


            $hotel = (new Hotel())
                ->setName($faker->name())
                ->setCity($faker->city())
                ->setCountry($faker->country())
                ->setAddress($faker->address())
                ->setDescription($faker->text(100))
                ->setCharacteristics($faker->text(50))
                ->setInformations($faker->text(50))
                ->setPhoto("photo.png")
                ->setSlug($faker->slug())
                ;
            $manager->persist($hotel);


            $user = (new User())
                ->setEmail("user$i@gmail.com")
                ->setPassword("password")
                ->setRoles(["ROLE_USER"])
                ;
            $manager->persist($user);
            
            
            $booking = (new Booking())
                ->setCustomers($faker->numberBetween(1, 6))
                ->setArrivalAt(new \DateTime())
                ->setDepartureAt((new \DateTime())->add(new \DateInterval("P1D")))
                ->setHotel($hotel)
                ->setBedroomType($faker->numberBetween(1, 6))
                ->setUser($user)
                ->setBedroomType($faker->numberBetween(1, 20))
                ;
            $manager->persist($booking);
        }

        $manager->flush();

        
    }
}
