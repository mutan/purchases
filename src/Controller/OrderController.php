<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Form\ProductUserData;
use App\Form\ProductUserType;
use App\Services\ShopHelper;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/order")
 * @IsGranted("ROLE_USER")
 */
class OrderController extends BaseController
{
    /**
     * Автодополнение поля shop в форме создания/редактирования заказа
     * @Route("/shop/autocomplete", name="order_shop_autocomplite", methods={"GET","POST"})
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
     * @Route("/", name="order_index", methods={"GET"})
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function index(OrderRepository $orderRepository): Response
    {
        return $this->render('order/index.html.twig', [
            'orders' => $orderRepository->findAllByUser($this->getUser()),
        ]);
    }

    /**
     * Форма создания заказа (ajax)
     * @Route("/new", name="order_new", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $order = new Order();
        $orderForm = $this->createForm(OrderType::class, $order);
        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            $order->setUser($this->getUser());
            $this->getEm()->persist($order);
            $this->getEm()->flush();
            $this->addFlash('success', "Заказ {$order->getIdWithPrefix()} создан. Теперь добавьте в него товары!");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('order/_new_modal.html.twig', [
                'orderForm' => $orderForm->createView(),
            ])
        ], 200);
    }

    /**
     * Страница с одним заказом и списком продуктов в нем для пользователя
     * @Route("/{order_id}", name="order_show", methods={"GET"})
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(Request $request, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findWithRelations($request->get('order_id'));
        $this->denyAccessUnlessGranted('ORDER_EDIT', $order);

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * Форма редактирования заказа пользователем (ajax)
     * @Route("/{order_id}/edit", name="order_edit", methods={"POST"})
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findWithRelations($request->get('order_id'));
        $this->denyAccessUnlessGranted('ORDER_EDIT', $order);

        $order = new Order();
        $orderForm = $this->createForm(OrderType::class, $order);
        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            $this->getEm()->flush();
            $this->addFlash('success', "Заказ {$order->getIdWithPrefix()} обновлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('order/_edit_modal.html.twig', [
                'order' => $order,
                'orderForm' => $orderForm->createView(),
            ])
        ], 200);
    }

    /**
     * Удаление заказа
     * @Route("/{id}/delete", name="order_delete", methods={"GET"})
     * @param Request $request
     * @param Order $order
     * @return Response
     */
    public function delete(Request $request, Order $order): Response
    {
        $this->denyAccessUnlessGranted('ORDER_EDIT', $order);

        if (!$order->getProducts()->isEmpty()) {
            $this->addFlash('warning','Заказ можно удалить, только если он не содержит товаров.');
            return $this->redirect($request->headers->get('referer'));
        }

        $order->setStatus(Order::STATUS_DELETED);
        $this->getEm()->persist($order);
        $this->getEm()->flush();
        $this->addFlash('success','Заказ удален.');

        return $this->redirectToRoute('order_index');
    }

    /**
     * Форма создания товара (ajax)
     * @Route("/{order_id}/product/new", name="order_product_new", methods={"POST"})
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function newProduct(Request $request, OrderRepository $orderRepository): Response
    {
        $productData = new ProductUserData();
        $productForm = $this->createForm(ProductUserType::class, $productData);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $product = new Product();
            $order = $orderRepository->findWithRelations($request->get('order_id'));
            $productData->fill($product);
            $product->setUser($this->getUser());
            $product->setOrder($order);
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
     * @Route("/product/{product_id}/edit", name="product_edit", methods={"POST"})
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
     * @Route("/product/{id}", name="order_product_delete", methods={"DELETE"})
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

        return $this->redirectToRoute('order_show', ['order_id' => $product->getOrder()->getId()]);
    }
}
