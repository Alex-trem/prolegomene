<?php

namespace App\Controller;

use Calendar\Month;
use App\Entity\Hotel;
use Twig\Environment;
use App\Entity\Review;
use App\Entity\Bedroom;
use App\Entity\Booking;
use App\Form\NullFormType;
use App\Form\ReviewFormType;
use App\Form\BookingFormType;
use App\Repository\HotelRepository;
use App\Repository\ReviewRepository;
use App\Repository\BedroomRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HotelsController extends AbstractController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    #[Route('/', name: 'home')]
    public function index(Request $request, HotelRepository $hotelRepository): Response
    {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $hotelRepository->getHotelPaginator($offset);

        return $this->render('hotels/index.html.twig', [
            'hotels' => $paginator,
            'previous' => $offset - HotelRepository::PAGINATOR_PER_PAGE,
            'next'  => min(count($paginator), $offset + HotelRepository::PAGINATOR_PER_PAGE),
        ]);
    }

    #[Route('/booking/{slug}/{month}/{year}', name:'booking')]
    public function booking(Request $request, EntityManagerInterface $entityManager, Hotel $hotel, BookingRepository $bookingRepo, BedroomRepository $bedroomRepo, ReviewRepository $reviewRepo): Response
    {
        //-- CALENDRIER --//

        $curentMonth = (int) $request->attributes->get('month');
        $currentYear = (int) $request->attributes->get('year');
        $month = new Month($curentMonth ?? null, $currentYear ?? null);
        $currentMonth = $month->toString();
        $weeks = $month->getWeeks();
        $days = $month->days;
        $start = $month->getStartingDay();
        $start = $start->format('N') === '1' ? $start : $month->getStartingDay()->modify('last monday');
        $end = (clone $start)->modify('+' . (6 + 7 * ($weeks -1)) . ' days');
        $bookings = $bookingRepo->getBookingsBetweenByDay($start, $end, $hotel->id);
        if($bookings){
            foreach($bookings as $book) {
                foreach($book as $b) {
                    $unavailableBedrooms[] = $b->bedroomType;
                }
            }
        }
        $bedrooms = $bedroomRepo->findAll();
        $dispo = '';
        if(isset($unavailableBedrooms)){
            $dispo = sizeof($bedrooms) - sizeof($unavailableBedrooms);
        }
        
        //-- FORMULAIRE BOOKING --//

        $bedrooms = $bedroomRepo->findAll();
        $booking = new Booking();
        $bookingForm = $this->createForm(BookingFormType::class, $booking, [
            'bedrooms' => $bedrooms
        ]);
        $bookingForm->handleRequest($request);
        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {
            $booking->setHotelId($hotel->id);
            $booking->setUser($this->getUser());

            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->redirectToRoute('booking', ['slug' => $hotel, 'month' => $currentMonth, 'year' => $currentYear]);
        }

        //-- FORMULAIRE REVIEWS --//

        $reviewForm = $this->createForm(NullFormType::class);

        if (!is_null($this->getUser())) {
            $review = new Review();
            $reviewForm = $this->createForm(ReviewFormType::class, $review, [
                'data_class' => Review::class,
                'user' => $this->getUser()
            ]);
            $reviewForm->handleRequest($request);
            if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
                $review->setHotel($hotel);
                $review->setUser($this->getUser());

                $entityManager->persist($review);
                $entityManager->flush();

                return $this->redirectToRoute('booking', ['slug' => $hotel, 'month' => $currentMonth, 'year' => $currentYear]);
            }
        }

        $reviews = $reviewRepo->findBy(['hotel' => $hotel->id]);
        // dd($reviews);

        return new Response($this->twig->render('hotels/booking.html.twig', [
            'hotel' => $hotel,
            'currentMonth' => $currentMonth,
            'weeks' => $weeks,
            'days' => $days,
            'start' => $start,
            'end' => $end,
            'bookings' => $bookings,
            'bedrooms' => $bedrooms,
            'dispo' => $dispo,
            'previousMonth' => $month->previousMonth()->month,
            'nextMonth' => $month->nextMonth()->month,
            'booking_form' => $bookingForm->createView(),
            'review_form' => $reviewForm->createView(),
            'reviews' => $reviews,
        ]));
    }
}
