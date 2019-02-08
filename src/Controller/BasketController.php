<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Form\BasketType;
use App\Repository\BasketRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/basket")
 * @IsGranted("ROLE_USER")
 */
class BasketController extends BaseController
{
    const SHOP_LIST_FOR_AUTOCOMPLETE = [
        'amazon.com',
        'coolstuffinc.com',
        'ebay.com',
        'magiccardmarket.com',
        'originalmagicart.store',
        'starcitygames.com',
        'trollandtoad.com',
    ];

    /**
     * @Route("/", name="basket_index", methods={"GET","POST"})
     */
    public function index(BasketRepository $basketRepository, Request $request): Response
    {
        $basket = new Basket();
        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $basket->setUser($this->getUser());
            $entityManager->persist($basket);
            $entityManager->flush();

            return $this->redirectToRoute('basket_index');
        }

        return $this->render('basket/index.html.twig', [
            'baskets' => $basketRepository->findAllByUser($this->getUser()),
            'basket' => $basket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/autocomplete", name="basket_shop_autocomplite", methods={"GET","POST"})
     */
    public function autocomplete(Request $request, $debugLogPath, $debugLogFile): Response
    {
        if (null == $request->query->get('term')) {
            throw $this->createNotFoundException('Неверный запрос.');
        }

        $term = $request->query->get('term');


        $shops = array_filter(self::SHOP_LIST_FOR_AUTOCOMPLETE, function ($shop) use ($term) {
            return (stripos($shop, $term) !== false);
        });

        return $this->json($shops);
    }

    /**
     * @Route("/new", name="basket_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $basket = new Basket();
        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $basket->setUser($this->getUser());
            $entityManager->persist($basket);
            $entityManager->flush();

            return $this->redirectToRoute('basket_index');
        }

        return $this->render('basket/new.html.twig', [
            'basket' => $basket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="basket_show", methods={"GET"})
     */
    public function show(Basket $basket, BasketRepository $basketRepository, Request $request): Response
    {
        $this->denyAccessUnlessGranted('BASKET_MANAGE', $basket);

        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $basket->setUser($this->getUser());
            $entityManager->persist($basket);
            $entityManager->flush();

            return $this->redirectToRoute('basket_index');
        }

        return $this->render('basket/show.html.twig', [
            'basket' => $basket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="basket_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Basket $basket): Response
    {
        $this->denyAccessUnlessGranted('BASKET_MANAGE', $basket);

        $form = $this->createForm(BasketType::class, $basket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('basket_index', [
                'id' => $basket->getId(),
            ]);
        }

        return $this->render('basket/edit.html.twig', [
            'basket' => $basket,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="basket_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Basket $basket): Response
    {
        $this->denyAccessUnlessGranted('BASKET_MANAGE', $basket);

        if (!$basket->isNew()) {
            $this->addFlash('danger', "Статус заказа {$basket->getIdWithPrefix()} – {$basket->getStatus()}. Удаление невозможно.");
            return $this->redirectToRoute('basket_index');
        }

        if ($this->isCsrfTokenValid('delete'.$basket->getId(), $request->request->get('_token'))) {
            $basket->setStatus(BASKET::STATUS_DELETED);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($basket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('basket_index');
    }
}
