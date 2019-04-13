<?php

namespace App\Controller;

use App\Helpers\LitemfApiService;
use App\Repository\UserAddressRepository;
use App\Repository\UserPassportRepository;
use Mutan\HelperBundle\LoggerAwareTrait;
use Mutan\HelperBundle\TokenGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    use LoggerAwareTrait;
    /**
     * @Route("/", name="app_homepage")
     * @param LitemfApiService $litemfApiService
     * @param UserAddressRepository $userAddressRepository
     * @param UserPassportRepository $userPassportRepository
     * @param TokenGenerator $tokenGenerator
     * @return Response
     */
    public function index(LitemfApiService $litemfApiService, UserAddressRepository $userAddressRepository, UserPassportRepository $userPassportRepository, TokenGenerator $tokenGenerator) : Response
    {
        dump($tokenGenerator->generateToken(64)); die('ok');


        $userAddres   = $userAddressRepository->find(2);
        $userPassport = $userPassportRepository->find(12);

        //$litemfApiService->createAddress($userAddres, $userPassport);

        return $this->render('welcome/index.html.twig', [

        ]);
    }
}
