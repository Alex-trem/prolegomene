<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/user', name: 'user')]
    public function index(BookingRepository $bookingRepo, ReviewRepository $reviewRepo): Response
    {
        $user = $this->getUser();

        if (is_null($user)){
            return $this->redirectToRoute('app_login');
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
