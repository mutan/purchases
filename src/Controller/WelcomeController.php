<?php

namespace App\Controller;

use App\Helpers\LitemfApiService;
use App\Repository\UserAddressRepository;
use App\Repository\UserPassportRepository;
use Mutan\HelperBundle\Service\TokenGenerator;
use Mutan\HelperBundle\Traits\LoggerAwareTrait;
use Mutan\HelperBundle\Traits\ParameterBagAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    use LoggerAwareTrait, ParameterBagAwareTrait;
    /**
     * @Route("/", name="app_homepage")
     * @param LitemfApiService $litemfApiService
     * @param UserAddressRepository $userAddressRepository
     * @param UserPassportRepository $userPassportRepository
     * @param TokenGenerator $tokenGenerator
     * @return Response
     */
    public function index(LitemfApiService $litemfApiService, UserAddressRepository $userAddressRepository, UserPassportRepository $userPassportRepository, TokenGenerator $tokenGenerator, $debugLogFile) : Response
    {
        dump($tokenGenerator->getCustomPassword(10, 0, 0, 10, 0)); die('ok');


        $userAddres   = $userAddressRepository->find(2);
        $userPassport = $userPassportRepository->find(12);

        //$litemfApiService->createAddress($userAddres, $userPassport);

        return $this->render('welcome/index.html.twig', [

        ]);
    }
}
