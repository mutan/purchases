<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/privacy_policy")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_privacy_policy", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('user/privacy_policy.html.twig');
    }
}
