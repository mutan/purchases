<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Product;
use App\Form\BasketType;
use App\Form\ProductType;
use App\Helpers\ShopHelper;
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
    /**
     * @Route("/autocomplete", name="basket_shop_autocomplite", methods={"GET","POST"})
     */
    public function autocomplete(Request $request): Response
    {
        if (null == $request->query->get('term')) {
            throw $this->createNotFoundException('Неверный запрос.');
        }

        $term = $request->query->get('term');
        $shops = array_filter(ShopHelper::SHOP_LIST_FOR_AUTOCOMPLETE, function ($shop) use ($term) {
            return (stripos($shop, $term) !== false);
        });

        return $this->json($shops);
    }

    /**
     * @Route("/", name="basket_index", methods={"GET","POST"})
     */
    public function index(BasketRepository $basketRepository, Request $request): Response
    {
        $modalBasketNewShow = false;

        $basket = new Basket();
        $basketForm = $this->createForm(BasketType::class, $basket);
        $basketForm->handleRequest($request);

        if ($basketForm->isSubmitted()) {
            if ($basketForm->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $basket->setUser($this->getUser());
                $entityManager->persist($basket);
                $entityManager->flush();

                return $this->redirectToRoute('basket_index');
            } else {
                $modalBasketNewShow = true;
            }
        }

        return $this->render('basket/index.html.twig', [
            'baskets' => $basketRepository->findAllByUser($this->getUser()),
            'basket' => $basket,
            'basketForm' => $basketForm->createView(),
            'modalBasketNewShow' => $modalBasketNewShow,
        ]);
    }

    /**
     * @Route("/{id}", name="basket_show", methods={"GET","POST"})
     */
    public function show(Basket $basket, Request $request): Response
    {
        $modalBasketEditShow = false;
        $modalProductNewShow = false;

        /* BASKET EDIT */
        $basketForm = $this->createForm(BasketType::class, $basket);
        $basketForm->handleRequest($request);

        if ($basketForm->isSubmitted()) {
            if ($basketForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('basket_show', ['id' => $basket->getId()]);
            } else {
                $modalBasketEditShow = true;
            }
        }

        /* PRODUCT NEW */
        $product = new Product();
        $productForm = $this->createForm(ProductType::class, $product);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted()) {
            if ($productForm->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $product->setUser($this->getUser());
                $product->setBasket($basket);
                $entityManager->persist($product);
                $entityManager->flush();

                return $this->redirectToRoute('basket_show', ['id' => $basket->getId()]);
            } else {
                $modalProductNewShow = true;
            }
        }

        return $this->render('basket/show.html.twig', [
            'basket' => $basket,
            'basketForm' => $basketForm->createView(),
            'product' => $product,
            'productForm' => $productForm->createView(),
            'modalBasketEditShow' => $modalBasketEditShow,
            'modalProductNewShow' => $modalProductNewShow,
        ]);
    }

    /**
     * @Route("/{id}", name="basket_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Basket $basket): Response
    {
        $this->denyAccessUnlessGranted('BASKET_MANAGE', $basket);

        if (!$basket->getProducts()->isEmpty()) {
            $this->addFlash('danger','Заказ можно удалить, только если он не содержит товаров.');
            return $this->redirect($request->headers->get('referer'));
        }

        if ($this->isCsrfTokenValid('delete'.$basket->getId(), $request->request->get('_token'))) {
            $basket->setStatus(Basket::STATUS_DELETED);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($basket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('basket_index');
    }
}
