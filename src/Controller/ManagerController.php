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
     * @param Request $request
     * @return Response
     */
    public function basket(Basket $basket, Request $request) : Response
    {
        $this->denyAccessUnlessGranted('BASKET_MANAGE', $basket);

        $modalBasketManageShow = false;

        /* BASKET EDIT */
        $basketForm = $this->createForm(BasketType::class, $basket);
        $basketForm->handleRequest($request);

        if ($basketForm->isSubmitted()) {
            dump($basketForm); die('ok');
            if ($basketForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('manager_basket_show', ['id' => $basket->getId()]);
            } else {
                $modalBasketManageShow = true;
            }
        }

        return $this->render('manager/basket_show.html.twig', [
            'basket' => $basket,
            'basketForm' => $basketForm->createView(),
            'modalBasketManageShow' => $modalBasketManageShow,
        ]);
    }
}
