<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/info")
 */
class InfoController extends AbstractController
{
    /**
     * @Route("/privacy_policy", name="info_privacy_policy", methods={"GET"})
     */
    public function privacyPolicy(): Response
    {
        return $this->render('info/privacy_policy.html.twig');
    }

    /**
     * @Route("/what_and_where", name="info_what_and_where", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('info/what_and_where.html.twig');
    }

    /**
     * @Route("/delivery", name="info_delivery", methods={"GET"})
     */
    public function delivery(): Response
    {
        return $this->render('info/delivery.html.twig');
    }

    /**
     * @Route("/profit", name="info_profit", methods={"GET"})
     */
    public function profit(): Response
    {
        return $this->render('info/profit.html.twig');
    }
}
