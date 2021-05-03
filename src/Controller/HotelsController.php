<?php

namespace App\Controller;

use App\Entity\Hotel;
use Twig\Environment;
use App\Entity\Review;
use App\Entity\Booking;
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

    #[Route('/booking/{slug}', name: 'booking')]
    public function booking(
        Request $request,
        EntityManagerInterface $entityManager,
        Hotel $hotel,
        BookingRepository $bookingRepo,
        BedroomRepository $bedroomRepo,
        ReviewRepository $reviewRepo,
        BookingValidator $bookingValidator
    ): Response {
        $user = $this->getUser();

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

            if ($booking->arrivalAt->format('Y-m-d') < (new \DateTime())->format('Y-m-d')) {
                $this->addFlash('danger', 'The date can\'t be in the past');
                return $this->redirectToRoute('booking', ['slug' => $hotel]);
            }

            $keys[] = $booking->arrivalAt->format('Y-m-d');
            $keys[] = $booking->bedroomType;

            $existingBookings = $bookingValidator->exists($booking, $hotel);
            if ($existingBookings) {
                foreach ($existingBookings as $existingBooking) {
                    if (array_values($keys) === array_values($existingBooking)) {
                        $this->addFlash('danger', 'This room is no longer available for these dates');
                        return $this->redirectToRoute('booking', ['slug' => $hotel]);
                    }
                }
            }

            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Your booking has been registered');

            return $this->redirectToRoute('booking', ['slug' => $hotel]);
        }

        //-- FORMULAIRE REVIEWS --//

        $bookingsFormUser = [];
        if ($user){
            $bookingsFormUser = $bookingRepo->findByHotelAndUser($hotel, $user);
        }
    
        $review = new Review();
        $reviewForm = $this->createForm(ReviewFormType::class, $review, [
            'bookings' => $bookingsFormUser
        ]);
        $reviewForm->handleRequest($request);
        if ($reviewForm->isSubmitted() && $reviewForm->isValid()) {
            $review->setHotel($hotel);
            if ($user){
                $review->setUser($user);
            }

            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Your comment has been published');

            return $this->redirectToRoute('booking', ['slug' => $hotel]);
        }


        $reviews = $reviewRepo->findBy(['hotel' => $hotel]);

        //-- CALENDRIER --//

        $bookingsFound = $bookingRepo->findAll();
        $bookingsByDay = [];
        foreach ($bookingsFound as $booking) {
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
                    $bookingsData[] = $booking;
                }
            }
        }
        
        foreach($bookingsData as $booking){
            $unavailables[] = [
                'date' => $booking['start'],
                'type' => $booking['title']
            ];
        }

        $bookingsData = json_encode($bookingsData);
        $unavailables = json_encode($unavailables);

        return new Response($this->twig->render('hotels/booking.html.twig', [
            'hotel' => $hotel,
            'bedrooms' => $bedrooms,
            'booking_form' => $bookingForm->createView(),
            'review_form' => $reviewForm->createView(),
            'reviews' => $reviews,
            'bookingsData' => $bookingsData,
            'dataB' => $unavailables
        ]));
    }
}
