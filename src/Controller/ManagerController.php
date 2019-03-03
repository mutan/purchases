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
     * @Route("/baskets", name="manager_basket_list", methods={"GET"})
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function baskets(BasketRepository $basketRepository) : Response
    {
        return $this->render('manager/basket_list.html.twig', [
            'baskets' => $basketRepository->findAllByManager($this->getUser()),
        ]);
    }

    /**
     * @Route("/basket/{id}", name="manager_basket_show", methods={"GET","POST"})
     * @param Basket $basket
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function basket(Basket $basket, BasketRepository $basketRepository) : Response
    {
        $this->denyAccessUnlessGranted('BASKET_MANAGE', $basket);

        return $this->render('manager/basket_show.html.twig', [
            'baskets' => $basketRepository->findAllByManager($this->getUser()),
        ]);
    }
}
