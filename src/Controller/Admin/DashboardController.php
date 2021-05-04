<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Hotel;
use App\Entity\Review;
use App\Entity\Bedroom;
use App\Entity\Booking;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    private $bookingRepo;

    public function __construct(BookingRepository $bookingRepo)
    {
        $this->bookingRepo = $bookingRepo;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $bookingsFound = $this->bookingRepo->getBookingsData();
        $bookingsData = json_encode($bookingsFound['calendar']);

        return $this->render('admin/index.html.twig', [
            'dashboard_controller_filepath' => (new \ReflectionClass(static::class))->getFileName(),
            'dashboard_controller_class' => (new \ReflectionClass(static::class))->getShortName(),
            'bookingsData' => $bookingsData,
        ]);
    }

    public function configureCrud(): Crud
    {
        return Crud::new()
            ->overrideTemplate('layout', 'admin/layout.html.twig')
        ;
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
