<?php

namespace App\Controller;

use App\Helpers\ApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     * @return Response
     */
    public function index() : Response
    {
        return $this->render('welcome/index.html.twig', [

        ]);
    }
}
