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

    public function getBookingsBetweenByDay ($bookings, bool $filter = false): ?array 
    {
        $bookingsByDay = [];
        if ($filter = false) {
            foreach ($bookings as $booking) {
                $date = $booking->arrivalAt->format('Y-m-d');
                if (empty($bookingsByDay[$date])) {
                    $bookingsByDay[$date] = [$booking];
                } else {
                    array_push($bookingsByDay[$date], $booking);
                }
            }
            return $bookingsByDay;
        } 
        else {
            foreach ($bookings as $booking) {
                $date = $booking->arrivalAt->format('Y-m-d');
                if (empty($bookingsByDay[$date])) {
                    $bookingsByDay[$date][0] = [
                        'start' => $booking->arrivalAt->format('Y-m-d'),
                        'end' => $booking->departureAt->format('Y-m-d'),
                        'title' => $booking->bedroomType,
                    ];
                } else {
                    array_push($bookingsByDay[$date], [
                        'start' => $booking->arrivalAt->format('Y-m-d'),
                        'end' => $booking->departureAt->format('Y-m-d'),
                        'title' => $booking->bedroomType,
                    ]);
                }
            }
            return $bookingsByDay;
        }
    }

    /**
     * Undocumented function
     *
     * @param Hotel $hotel
     * @param User $user
     * @return array
     */
    public function findByHotelAndUser(Hotel $hotel, User $user): array
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

    public function findAllArray(Hotel $hotel)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.hotel = :hotel')
            ->setParameter('hotel', $hotel)
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY)
            ;
    }

    public function getBookingsData(){
        $bookingsFound = $this->findAll();
        $bookingsByDay = $this->getBookingsBetweenByDay($bookingsFound, true);

        if (!empty($bookingsByDay)){
            foreach ($bookingsByDay as $key => $bookings){
                if (sizeof($bookings) === 3){
                    foreach ($bookings as $k => $booking){
                        $bookingsByDay[$key][$k]['backgroundColor'] = 'orange';
                        $bookingsByDay[$key][$k]['borderColor'] = 'orange';
                        $bookingsByDay[$key][$k]['textColor'] = 'black';
                    }
                } elseif (sizeof($bookings) === 6){
                    foreach ($bookings as $k => $booking){
                        $bookingsByDay[$key][$k]['backgroundColor'] = 'red';
                        $bookingsByDay[$key][$k]['borderColor'] = 'red';
                        $bookingsByDay[$key][$k]['textColor'] = 'black';
                    }
                }
            }
            foreach ($bookingsByDay as $bookings){
                foreach ($bookings as $booking){
                    $bookingsData['calendar'][] = $booking;
                }
            }
        }
        
        foreach($bookingsData['calendar'] as $booking){
            $bookingsData['form']['dateA'][] = $booking['start'];
            $bookingsData['form']['dateD'][] = $booking['end'];
            $bookingsData['form']['type'][] = $booking['title'];
        }

        return $bookingsData;
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
