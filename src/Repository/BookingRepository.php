<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Booking;
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
     * @param int $hotelId
     * @return Booking[]
     */
    public function getBookingsBetween (\DateTime $start, \DateTime $end, int $hotelId) 
    {
        return $this->createQueryBuilder('b')
            ->andWhere("b.arrivalAt BETWEEN '{$start->format('Y-m-d')}' AND '{$end->format('Y-m-d')}'")
            ->andWhere("b.hotelId = :hotelId")
            ->setParameter("hotelId", $hotelId)
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * Récupère les évènements commençant entre 2 dates indexé par jour
     * @param \DateTime $start
     * @param \DateTime $end
     * @param int $hotelId
     * @return array
     */
    public function getBookingsBetweenByDay (\DateTime $start, \DateTime $end, int $hotelId): ?array 
    {
        $bookings = $this->getBookingsBetween($start, $end, $hotelId);
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

    public function findByHotelAndUser(int $hotelId, int $userId)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.hotelId = :hotelId')
            ->andWhere('b.userId = :userId')
            ->setParameter('hotelId', $hotelId)
            ->setParameter('userId', $userId)
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
