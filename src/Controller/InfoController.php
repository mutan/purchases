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
    public function index(): Response
    {
        return $this->render('info/privacy_policy.html.twig');
    }
}
