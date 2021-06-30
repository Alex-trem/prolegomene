<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends AbstractController
{

    #[Route('/sitemap.xml', name: 'sitemap')]
    public function index() {
        return new Response("sitemap", 200, ["Content-Type" => "text/xml"]);
    }

}