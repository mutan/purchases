<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     * @return Response
     */
    public function index() : Response
    {
        return $this->render('info/index.html.twig', []);
    }

    /**
     * @Route("/info/privacy_policy", name="info_privacy_policy", methods={"GET"})
     */
    public function privacyPolicy(): Response
    {
        return $this->render('info/privacy_policy.html.twig');
    }

    /**
     * @Route("/info/what_and_where", name="info_what_and_where", methods={"GET"})
     */
    public function whatAndWhere(): Response
    {
        return $this->render('info/what_and_where.html.twig');
    }

    /**
     * @Route("/info/delivery", name="info_delivery", methods={"GET"})
     */
    public function delivery(): Response
    {
        return $this->render('info/delivery.html.twig');
    }

    /**
     * @Route("/info/profit", name="info_profit", methods={"GET"})
     */
    public function profit(): Response
    {
        return $this->render('info/profit.html.twig');
    }
}
