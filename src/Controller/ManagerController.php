<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Form\BasketType;
use App\Repository\BasketRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/manager")
 * @IsGranted("ROLE_MANAGER")
 */
class ManagerController extends AbstractController
{
    /**
     * @Route("/baskets", name="manager_baskets")
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function baskets(BasketRepository $basketRepository) : Response
    {
        return $this->render('manager/baskets.html.twig', [
            'baskets' => $basketRepository->findAllByManager($this->getUser()),
        ]);
    }
}
