<?php

namespace App\Controller;

use App\Entity\LogMovement;
use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Form\ProductUserType;
use App\Resources\ShopHelper;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Services\LogMovementService;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
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
     * @Route("/", name="user_order_index", methods={"GET"})
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
     * @Route("/new", name="user_order_new", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function new(Request $request): JsonResponse
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $order->setUser($this->getUser());
            $this->getEm()->persist($order);
            $this->getEm()->flush();
            $this->container->get(LogMovementService::class)->addEventForOrder(LogMovement::ORDER_CREATED, $order, $this->getUser());
            $this->addFlash('success', "Заказ {$order->getIdWithPrefix()} создан. Теперь добавьте в него товары!");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('order/_order_modal.html.twig', [
                'form' => $form->createView(),
                'title' => 'Новый заказ'
            ])
        ], 200);
    }

    /**
     * Страница с одним заказом и списком продуктов в нем для пользователя
     * @Route("/{order_id}", name="user_order_show", methods={"GET"})
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function show(Request $request, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findWithRelations($request->get('order_id'));
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * Форма редактирования заказа пользователем (ajax)
     * @Route("/{order_id}/edit", name="user_order_edit", methods={"POST"})
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findWithRelations($request->get('order_id'));
        $this->denyAccessUnlessGranted('ORDER_EDIT', $order);

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
            'output' => $this->renderView('order/_order_modal.html.twig', [
                'title' => "Редактировать заказ {$order->getIdWithPrefix()}",
                'order' => $order,
                'form' => $orderForm->createView(),
            ])
        ], 200);
    }

    /**
     * Утвердить заказ (ajax)
     * @Route("/{id}/approve", name="user_order_approve", methods={"POST"})
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws Exception
     */
    public function approve(Request $request, Order $order): JsonResponse
    {
        $this->denyAccessUnlessGranted('ORDER_APPROVE', $order);

        $order->setStatus(Order::STATUS_APPROVED);
        $this->getEm()->persist($order);
        $this->getEm()->flush();
        $this->container->get(LogMovementService::class)->addEventForOrder(
            LogMovement::ORDER_STATUS_CHANGED,
            $order,
            $this->getUser(),
            LogMovement::TYPES_MAP[LogMovement::ORDER_STATUS_CHANGED] . ': ' . Order::STATUS_APPROVED
        );
        $this->addFlash('success',"Заказ {$order->getIdWithPrefix()} утвержден.");

        return new JsonResponse(['message' => 'Success',], 200);
    }

    /**
     * Вернуть заказ в статус Новый (ajax)
     * @Route("/{id}/return_to_new", name="user_order_return_to_new", methods={"POST"})
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws Exception
     */
    public function returnToNew(Request $request, Order $order): JsonResponse
    {
        $this->denyAccessUnlessGranted('ORDER_RETURN_TO_NEW', $order);

        $order->setStatus(Order::STATUS_NEW);
        $this->getEm()->persist($order);
        $this->getEm()->flush();
        $this->container->get(LogMovementService::class)->addEventForOrder(
            LogMovement::ORDER_STATUS_CHANGED,
            $order,
            $this->getUser(),
            LogMovement::TYPES_MAP[LogMovement::ORDER_STATUS_CHANGED] . ': ' . Order::STATUS_NEW
        );
        $this->addFlash('success',"Заказ {$order->getIdWithPrefix()} возвращен в статус Новый.");

        return new JsonResponse(['message' => 'Success',], 200);
    }

    /**
     * Установить заказ в статус Выкупается (ajax)
     * @Route("/{id}/set_redeemed", name="user_order_set_redeemed", methods={"POST"})
     * @param Request $request
     * @param Order $order
     * @return JsonResponse
     * @throws Exception
     */
    public function setRedeemed(Request $request, Order $order): JsonResponse
    {
        $this->denyAccessUnlessGranted('ORDER_SET_REDEEMED', $order);

        $order->setStatus(Order::STATUS_REDEEMED);
        $this->getEm()->persist($order);
        $this->getEm()->flush();
        $this->container->get(LogMovementService::class)->addEventForOrder(
            LogMovement::ORDER_STATUS_CHANGED,
            $order,
            $this->getUser(),
            LogMovement::TYPES_MAP[LogMovement::ORDER_STATUS_CHANGED] . ': ' . Order::STATUS_REDEEMED
        );
        $this->addFlash('success',"Заказ {$order->getIdWithPrefix()} переведен в статус Выкупается.");

        return new JsonResponse(['message' => 'Success',], 200);
    }

    /**
     * Удаление заказа
     * @Route("/{id}/delete", name="user_order_delete", methods={"DELETE"})
     * @param Request $request
     * @param Order $order
     * @return Response
     * @throws Exception
     */
    public function delete(Request $request, Order $order): Response
    {
        $this->denyAccessUnlessGranted('ORDER_DELETE', $order);

        if ($order->hasProducts()) {
            $this->addFlash('warning','Заказ можно удалить, только если он не содержит товаров.');
            return $this->redirect($request->headers->get('referer'));
        }

        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $order->setStatus(Order::STATUS_DELETED);
            $id = $order->getIdWithPrefix();
            $this->getEm()->persist($order);
            $this->getEm()->flush();
            $this->container->get(LogMovementService::class)->addEventForOrder(LogMovement::ORDER_DELETED, $order, $this->getUser());
            $this->addFlash('success',"Заказ {$id} удален.");
        }

        return $this->redirectToRoute('user_order_index');
    }

    /**
     * Форма создания товара (ajax)
     * @Route("/{order_id}/product/new", name="user_order_product_new", methods={"POST"})
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return JsonResponse
     * @throws NonUniqueResultException
     * @throws Exception
     */
    public function newProduct(Request $request, OrderRepository $orderRepository): JsonResponse
    {
        $product = new Product();
        $productForm = $this->createForm(ProductUserType::class, $product);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $order = $orderRepository->findWithRelations($request->get('order_id'));
            $product->setUser($this->getUser());
            $product->setOrder($order);
            $this->getEm()->persist($product);
            $this->getEm()->flush();
            $this->container->get(LogMovementService::class)->addEventForProduct(LogMovement::PRODUCT_CREATED, $product, $this->getUser());
            $this->addFlash('success', "Товар {$product->getIdWithPrefix()} добавлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('product/_product_modal.html.twig', [
                'title' => 'Новый товар',
                'form' => $productForm->createView(),
            ])
        ], 200);
    }

    /**
     * Форма редактирования товара (ajax)
     * @Route("/product/{id}/edit", name="user_order_product_edit", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function editProduct(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $product = $productRepository->find($request->get('id'));
        $this->denyAccessUnlessGranted('PRODUCT_EDIT_DELETE', $product);

        $productForm = $this->createForm(ProductUserType::class, $product);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $this->getEm()->flush();
            $this->addFlash('success', "Товар {$product->getIdWithPrefix()} обновлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('product/_product_modal.html.twig', [
                'title' => "Редактировать товар {$product->getIdWithPrefix()}",
                'product' => $product,
                'form' => $productForm->createView(),
            ])
        ], 200);
    }

    /**
     * Удаление товара
     * @Route("/product/{id}", name="user_order_product_delete", methods={"DELETE"})
     * @param Request $request
     * @param Product $product
     * @return Response
     * @throws Exception
     */
    public function deleteProduct(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT_DELETE', $product);

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $id = $product->getIdWithPrefix();
            $this->getEm()->beginTransaction();
            $this->getEm()->remove($product);
            $this->getEm()->flush();
            $this->container->get(LogMovementService::class)->addEventForProduct(LogMovement::PRODUCT_DELETED, $product, $this->getUser());
            $this->addFlash('success',"Товар {$id} удален.");
            $this->getEm()->commit();
        }

        return $this->redirectToRoute('user_order_show', ['order_id' => $product->getOrder()->getId()]);
    }
}
