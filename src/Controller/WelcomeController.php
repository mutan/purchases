<?php

namespace App\Controller;

use App\Helpers\ApiService;
use App\Helpers\LitemfApiService;
use App\Repository\UserAddressRepository;
use App\Repository\UserPassportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     * @return Response
     */
    public function index(LitemfApiService $litemfApiService, UserAddressRepository $userAddressRepository, UserPassportRepository $userPassportRepository) : Response
    {
        $userAddres   = $userAddressRepository->find(2);
        $userPassport = $userPassportRepository->find(12);

        $litemfApiService->createAddress($userAddres, $userPassport);

        return $this->render('welcome/index.html.twig', [

        ]);
    }
}
