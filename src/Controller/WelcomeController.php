<?php

namespace App\Controller;

use App\Services\LitemfApiService;
use App\Repository\UserAddressRepository;
use App\Repository\UserPassportRepository;
use App\Services\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     * @param LitemfApiService $litemfApiService
     * @param UserAddressRepository $userAddressRepository
     * @param UserPassportRepository $userPassportRepository
     * @param TokenGenerator $tokenGenerator
     * @param $debugLogFile
     * @return Response
     * @throws \Exception
     */
    public function index(LitemfApiService $litemfApiService, UserAddressRepository $userAddressRepository, UserPassportRepository $userPassportRepository, TokenGenerator $tokenGenerator, $debugLogFile) : Response
    {

        //$litemfApiService->createAddress($userAddres, $userPassport);

        return $this->render('welcome/index.html.twig', [

        ]);
    }
}
