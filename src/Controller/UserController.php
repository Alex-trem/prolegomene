<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(BookingRepository $bookingRepo, ReviewRepository $reviewRepo): Response
    {
        $user = $this->getUser();

        if (is_null($user)){
            return $this->redirectToRoute('home');
        }

        $bookings = $bookingRepo->findBy(['user' => $user]);
        $reviews = $reviewRepo->findBy(['user' => $user]);
        
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'bookings' => $bookings,
            'reviews' => $reviews,
        ]);
    }
}
