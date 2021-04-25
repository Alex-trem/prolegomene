<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Hotel;
use App\Entity\Review;
use App\Entity\Bedroom;
use App\Entity\Booking;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('HotelsBookings');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Hotels', 'fas fa-list', Hotel::class);
        yield MenuItem::linkToCrud('Bookings', 'fas fa-list', Booking::class);
        yield MenuItem::linkToCrud('Reviews', 'fas fa-list', Review::class);
        yield MenuItem::linkToCrud('Bedrooms', 'fas fa-list', Bedroom::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
    }
}
