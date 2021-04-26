<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Booking;
use App\Entity\Hotel;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Booking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Booking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Booking[]    findAll()
 * @method Booking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Récupère les évènements commençant entre 2 dates
     * @param \DateTime $start
     * @param \DateTime $end
     * @param Hotel $hotelId
     * @return Booking[]
     */
    public function getBookingsBetween (\DateTime $start, \DateTime $end, Hotel $hotel) 
    {
        return $this->createQueryBuilder('b')
            ->andWhere("b.arrivalAt BETWEEN '{$start->format('Y-m-d')}' AND '{$end->format('Y-m-d')}'")
            ->andWhere("b.hotel = :hotel")
            ->setParameter("hotel", $hotel)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Récupère les évènements commençant entre 2 dates indexé par jour
     * @param \DateTime $start
     * @param \DateTime $end
     * @param Hotel $hotel
     * @return array
     */
    public function getBookingsBetweenByDay (\DateTime $start, \DateTime $end, Hotel $hotel): ?array 
    {
        $bookings = $this->getBookingsBetween($start, $end, $hotel);
        if(!$bookings){
            return null;
        } 
        $days = [];
        foreach ($bookings as $booking) {
            $date = $booking->arrivalAt->format('Y-m-d');
            if (empty($days[$date])) {
                $days[$date] = [$booking];
            } else {
                array_push($days[$date], $booking);
            }
        }
        return $days;
    }

    /**
     * Undocumented function
     *
     * @param Hotel $hotel
     * @param User $user
     * @return void
     */
    public function findByHotelAndUser(Hotel $hotel, User $user)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.hotel = :hotel')
            ->andWhere('b.user = :user')
            ->setParameter('hotel', $hotel)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return Booking[] Returns an array of Booking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Booking
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
