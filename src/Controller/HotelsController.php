<?php

namespace App\Controller;

use App\Entity\Hotel;
use Twig\Environment;
use App\Entity\Review;
use App\Calendar\Month;
use App\Entity\Booking;
use App\Calendar\Calendar;
use App\Form\NullFormType;
use App\Form\ReviewFormType;
use App\Form\BookingFormType;
use App\Repository\HotelRepository;
use App\Validator\BookingValidator;
use App\Repository\ReviewRepository;
use App\Repository\BedroomRepository;
use App\Repository\BookingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/booking/{slug}', name:'booking')]
    public function booking(
        Request $request, 
        EntityManagerInterface $entityManager, 
        Hotel $hotel, 
        BookingRepository $bookingRepo, 
        BedroomRepository $bedroomRepo, 
        ReviewRepository $reviewRepo,
        BookingValidator $bookingValidator
    ): Response
    {
        $user = $this->getUser();

        //-- CALENDRIER --//

        $currentMonth = (new \DateTime())->format('m');
        $currentYear = (new \DateTime())->format('Y');
        $month = new Month($currentMonth ?? null, $currentYear ?? null);
        $currentMonthCalendar = $month->toString();
        $weeks = $month->getWeeks();
        $days = $month->days;
        $start = $month->getStartingDay();
        $start = $start->format('N') === '1' ? $start : $month->getStartingDay()->modify('last monday');
        $end = (clone $start)->modify('+' . (6 + 7 * ($weeks -1)) . ' days');
        $bookings = $bookingRepo->getBookingsBetweenByDay($start, $end, $hotel);
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

        $calendar = new Calendar();
        $make = $calendar->make();
        // $cell = $make['cells'];
        // $week_days_names = $make['week_days_names'];
        // $nbDeSemaines = ceil($calendar->get_days_count_in_month($make['year'], $make['month']) / 7);
        $dataPrevMonth = $calendar->get_prev_month($make['year'], $make['month']);
        $dataNextMonth = $calendar->get_next_month($make['year'], $make['month']);

        if ($request->get('ajax')){
            $currentMonth = (int) $request->get('month');
            $currentYear = (int) $request->get('year');
            $month = new Month($currentMonth, $currentYear);
            $currentMonthCalendar = $month->toString();
            $weeks = $month->getWeeks();
            $days = $month->days;
            $start = $month->getStartingDay();
            $start = $start->format('N') === '1' ? $start : $month->getStartingDay()->modify('last monday');
            $end = (clone $start)->modify('+' . (6 + 7 * ($weeks -1)) . ' days');
            $bookings = $bookingRepo->getBookingsBetweenByDay($start, $end, $hotel);
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

            return new JsonResponse(['content' => $this->twig->render('hotels/calendar.html.twig', [
                'dataNextMonth' => $dataNextMonth,
                'dataPrevMonth' => $dataPrevMonth,
                'currentMonth' => $currentMonthCalendar,
                'hotel' => $hotel,
                'previousMonth' => $month->previousMonth()->month,
                'nextMonth' => $month->nextMonth()->month,
                'days' => $days,
                'start' => $start,
                'bookings' => $bookings,
                'bedrooms' => $bedrooms,
                'weeks' => $weeks,
            ])]);
        }
        
        //-- FORMULAIRE BOOKING --//

        $bedrooms = $bedroomRepo->findAll();
        $booking = new Booking();
        $bookingForm = $this->createForm(BookingFormType::class, $booking, [
            'bedrooms' => $bedrooms
        ]);
        $bookingForm->handleRequest($request);
        if ($bookingForm->isSubmitted() && $bookingForm->isValid()) {
            $booking->setHotel($hotel);
            $booking->setUser($user);

            if ($booking->arrivalAt->format('Y-m-d') < (new \DateTime())->format('Y-m-d')){
                $this->addFlash('danger', 'The date can\'t be in the past');
                return $this->redirectToRoute('booking', ['slug' => $hotel, 'month' => $currentMonth, 'year' => $currentYear]);
            }

            $keys[] = $booking->arrivalAt->format('Y-m-d');
            $keys[] = $booking->bedroomType;

            $existingBookings = $bookingValidator->exists($booking, $hotel);
            foreach ($existingBookings as $existingBooking){
                if (array_values($keys) === array_values($existingBooking)) {
                    $this->addFlash('danger', 'This room is no longer available for these dates');
                    return $this->redirectToRoute('booking', ['slug' => $hotel, 'month' => $currentMonth, 'year' => $currentYear]);
                }
            }

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Your booking has been registered');

            return $this->redirectToRoute('booking', ['slug' => $hotel, 'month' => $currentMonth, 'year' => $currentYear]);
        }

        //-- FORMULAIRE REVIEWS --//

        $bookingsFormUser = [];
        if ($user){
            $bookingsFormUser = $bookingRepo->findByHotelAndUser($hotel, $user);
        }
        $reviewForm = $this->createForm(NullFormType::class);

        if (!is_null($user)) {
            $review = new Review();
            $reviewForm = $this->createForm(ReviewFormType::class, $review, [
                'bookings' => $bookingsFormUser
            ]);
            $reviewForm->handleRequest($request);
            if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
                $review->setHotel($hotel);
                $review->setUser($user);

                $entityManager->persist($review);
                $entityManager->flush();

                $this->addFlash('success', 'Your comment has been published');

                return $this->redirectToRoute('booking', ['slug' => $hotel, 'month' => $currentMonth, 'year' => $currentYear]);
            }
        }

        $reviews = $reviewRepo->findBy(['hotel' => $hotel]);
        // dd($reviews);

        

        return new Response($this->twig->render('hotels/booking.html.twig', [
            'dataNextMonth' => $dataNextMonth,
            'dataPrevMonth' => $dataPrevMonth,
            'hotel' => $hotel,
            'currentMonth' => $currentMonthCalendar,
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
