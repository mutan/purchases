<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Product;
use App\Form\BasketUserData;
use App\Form\BasketUserType;
use App\Form\ProductType;
use App\Helpers\ShopHelper;
use App\Repository\BasketRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 */
class BasketController extends BaseController
{
    /**
     * @Route("/basket/autocomplete", name="basket_shop_autocomplite", methods={"GET","POST"})
     * @param Request $request
     * @return Response
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
     * @Route("/basket/", name="basket_index", methods={"GET","POST"})
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function index(BasketRepository $basketRepository): Response
    {
        return $this->render('basket/index.html.twig', [
            'baskets' => $basketRepository->findAllByUser($this->getUser()),
        ]);
    }

    /**
     * @Route("/basket/new", name="basket_new", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $basketUserData = new BasketUserData();
        $basketForm = $this->createForm(BasketUserType::class, $basketUserData);
        $basketForm->handleRequest($request);

        if ($basketForm->isSubmitted() && $basketForm->isValid()) {
            $basket = new Basket();
            $basketUserData->fill($basket);
            $basket->setUser($this->getUser());
            $this->getEm()->persist($basket);
            $this->getEm()->flush();

            $this->addFlash('info', "Заказ {$basket->getIdWithPrefix()} создан. теперь добавьте в него товары!");

            $reload = true;

            //return $this->redirectToRoute('basket_index');
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('basket/_new_modal.html.twig', [
                'basketForm' => $basketForm->createView(),
            ])
        ], 200);
    }








    /**
     * @Route("/basket/{id}", name="basket_show", methods={"GET","POST"})
     * @param Basket $basket
     * @param Request $request
     * @return Response
     */
    public function show(Basket $basket, Request $request): Response
    {
        $this->denyAccessUnlessGranted('BASKET_SHOW', $basket);

        $modalBasketEditShow = false;
        $modalProductNewShow = false;

        /* BASKET EDIT */
        $basketUserData = new BasketUserData();
        $basketUserData->extract($basket);
        $basketForm = $this->createForm(BasketUserType::class, $basketUserData);
        $basketForm->handleRequest($request);

        if ($basketForm->isSubmitted()) {
            if ($basketForm->isValid()) {
                $basketUserData->fill($basket);
                $this->getEm()->flush();
                $this->addFlash('info', "Заказ {$basket->getIdWithPrefix()} обновлен.");
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
                $product->setUser($this->getUser());
                $product->setBasket($basket);
                $this->getEm()->persist($product);
                $this->getEm()->flush();

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
     * @Route("/basket/product/{id}/edit", name="basket_product_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function editProduct(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_MANAGE', $product);

        $form = $this->createForm(ProductType::class, $product, [
            'action' => $this->generateUrl('basket_product_edit', ['id' => $product->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEm()->flush();
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('product/_form_modal_content.html.twig', [
                'product' => $product,
                'productForm' => $form->createView(),
            ])
        ], 200);
    }

    /**
     * @Route("/basket/{id}", name="basket_delete", methods={"DELETE"})
     * @param Request $request
     * @param Basket $basket
     * @return Response
     */
    public function delete(Request $request, Basket $basket): Response
    {
        $this->denyAccessUnlessGranted('BASKET_DELETE', $basket);

        if (!$basket->getProducts()->isEmpty()) {
            $this->addFlash('danger','Заказ можно удалить, только если он не содержит товаров.');
            return $this->redirect($request->headers->get('referer'));
        }

        if ($this->isCsrfTokenValid('delete'.$basket->getId(), $request->request->get('_token'))) {
            $basket->setStatus(Basket::STATUS_DELETED);
            $this->getEm()->persist($basket);
            $this->getEm()->flush();

            $this->addFlash('info','Заказ удален.');
        }

        return $this->redirectToRoute('basket_index');
    }

    /**
     * @Route("/basket/product/{id}", name="basket_product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function deleteProduct(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_MANAGE', $product);

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->getEm()->remove($product);
            $this->getEm()->flush();

            $this->addFlash('info','Товар удален.');
        }

        return $this->redirectToRoute('basket_show', ['id' => $product->getBasket()->getId()]);
    }
}
