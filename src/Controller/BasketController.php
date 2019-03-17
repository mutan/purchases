<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Entity\Product;
use App\Form\BasketUserData;
use App\Form\BasketUserType;
use App\Form\ProductUserData;
use App\Form\ProductUserType;
use App\Helpers\ShopHelper;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
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
     * Автодополнение поля shop в форме создания/редактирования заказа
     * @Route("/basket/shop/autocomplete", name="user_basket_shop_autocomplite", methods={"GET","POST"})
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
     * Страница со списком заказов пользователя
     * @Route("/basket/", name="user_basket_index", methods={"GET"})
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
     * Форма создания заказа (ajax)
     * @Route("/basket/new", name="user_basket_new", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $basketUserData = new BasketUserData();
        $basketForm = $this->createForm(BasketUserType::class, $basketUserData);
        $basketForm->handleRequest($request);

        if ($basketForm->isSubmitted() && $basketForm->isValid()) {
            $basket = new Basket();
            $basketUserData->fill($basket);
            $basket->setUser($this->getUser());
            $this->getEm()->persist($basket);
            $this->getEm()->flush();
            $this->addFlash('success', "Заказ {$basket->getIdWithPrefix()} создан. Теперь добавьте в него товары!");
            $reload = true;
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
     * Страница с одним заказом и списком продуктов в нем для пользователя
     * @Route("/basket/{basket_id}", name="user_basket_show", methods={"GET"})
     * @param Request $request
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function show(Request $request, BasketRepository $basketRepository): Response
    {
        $basket = $basketRepository->findWithRelations($request->get('basket_id'));
        $this->denyAccessUnlessGranted('BASKET_OPERATE', $basket);

        return $this->render('basket/show.html.twig', [
            'basket' => $basket,
        ]);
    }

    /**
     * Форма редактирования заказа пользователем (ajax)
     * @Route("/basket/{basket_id}/edit", name="user_basket_edit", methods={"POST"})
     * @param Request $request
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function edit(Request $request, BasketRepository $basketRepository): Response
    {
        $basket = $basketRepository->findWithRelations($request->get('basket_id'));
        $this->denyAccessUnlessGranted('BASKET_OPERATE', $basket);

        $basketUserData = new BasketUserData();
        $basketUserData->extract($basket);
        $basketForm = $this->createForm(BasketUserType::class, $basketUserData);
        $basketForm->handleRequest($request);

        if ($basketForm->isSubmitted() && $basketForm->isValid()) {
            $basketUserData->fill($basket);
            $this->getEm()->flush();
            $this->addFlash('success', "Заказ {$basket->getIdWithPrefix()} обновлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('basket/_edit_modal.html.twig', [
                'basket' => $basket,
                'basketForm' => $basketForm->createView(),
            ])
        ], 200);
    }

    /**
     * Удаление заказа
     * @Route("/basket/{id}", name="user_basket_delete", methods={"DELETE"})
     * @param Request $request
     * @param Basket $basket
     * @return Response
     */
    public function delete(Request $request, Basket $basket): Response
    {
        $this->denyAccessUnlessGranted('BASKET_OPERATE', $basket);

        if (!$basket->getProducts()->isEmpty()) {
            $this->addFlash('warning','Заказ можно удалить, только если он не содержит товаров.');
            return $this->redirect($request->headers->get('referer'));
        }

        if ($this->isCsrfTokenValid('delete'.$basket->getId(), $request->request->get('_token'))) {
            $basket->setStatus(Basket::STATUS_DELETED);
            $this->getEm()->persist($basket);
            $this->getEm()->flush();
            $this->addFlash('success','Заказ удален.');
        }

        return $this->redirectToRoute('user_basket_index');
    }

    /**
     * Форма создания товара (ajax)
     * @Route("/basket/{basket_id}/product/new", name="user_basket_product_new", methods={"POST"})
     * @param Request $request
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function newProduct(Request $request, BasketRepository $basketRepository): Response
    {
        $productData = new ProductUserData();
        $productForm = $this->createForm(ProductUserType::class, $productData);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $product = new Product();
            $basket = $basketRepository->findWithRelations($request->get('basket_id'));
            $productData->fill($product);
            $product->setUser($this->getUser());
            $product->setBasket($basket);
            $this->getEm()->persist($product);
            $this->getEm()->flush();
            $this->addFlash('success', "Товар {$product->getIdWithPrefix()} добавлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('product/_new_modal.html.twig', [
                'productForm' => $productForm->createView(),
            ])
        ], 200);
    }

    /**
     * Форма редактирования товара (ajax)
     * @Route("/basket/product/{product_id}/edit", name="user_product_edit", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function editProduct(Request $request, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($request->get('product_id'));
        $this->denyAccessUnlessGranted('PRODUCT_OPERATE', $product);

        $productData = new ProductUserData();
        $productData->extract($product);
        $productForm = $this->createForm(ProductUserType::class, $productData);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $productData->fill($product);
            $this->getEm()->flush();
            $this->addFlash('success', "Товар {$product->getIdWithPrefix()} обновлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('product/_edit_modal.html.twig', [
                'product' => $product,
                'productForm' => $productForm->createView(),
            ])
        ], 200);
    }

    /**
     * Удаление товара
     * @Route("/basket/product/{id}", name="user_basket_product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function deleteProduct(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_OPERATE', $product);

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->getEm()->remove($product);
            $this->getEm()->flush();

            $this->addFlash('success','Товар удален.');
        }

        return $this->redirectToRoute('user_basket_show', ['basket_id' => $product->getBasket()->getId()]);
    }
}
