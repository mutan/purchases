<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderManagerType;
use App\Form\ProductManagerData;
use App\Form\ProductManagerType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/manager")
 * @IsGranted("ROLE_MANAGER")
 */
class ManagerController extends BaseController
{
    /**
     * Страница со списком заказов менеджера
     * @Route("/orders", name="manager_order_list", methods={"GET"})
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function orders(OrderRepository $orderRepository) : Response
    {
        return $this->render('manager/order_list.html.twig', [
            'orders' => $orderRepository->findAllByManager($this->getUser()),
        ]);
    }

    /**
     * Форма редактирования заказа менеджером (ajax)
     * @Route("/order/{order_id}/edit", name="manager_order_edit", methods={"POST"})
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function editOrder(Request $request, OrderRepository $orderRepository) : Response
    {
        $order = $orderRepository->findWithRelations($request->get('order_id'));
        $this->denyAccessUnlessGranted('ORDER_MANAGE', $order);

        $orderForm = $this->createForm(OrderManagerType::class, $order);
        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            $this->getEm()->flush();
            $this->addFlash('success', "Заказ {$order->getIdWithPrefix()} обновлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('manager/_order_edit_modal.html.twig', [
                'order' => $order,
                'orderForm' => $orderForm->createView(),
            ])
        ], 200);
    }

    /**
     * Страница с одним заказом и списком продуктов для менеджера
     * @Route("/order/{order_id}", name="manager_order_show", methods={"GET"})
     * @param Request $request
     * @param OrderRepository $orderRepository
     * @return Response
     */
    public function show(Request $request, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findWithRelations($request->get('order_id'));
        $this->denyAccessUnlessGranted('ORDER_MANAGE', $order);

        return $this->render('manager/order_show.html.twig', [
            'order' => $order,
        ]);
    }

    /**
     * Форма редактирования товара для менеджера (ajax)
     * @Route("/product/{product_id}/edit", name="manager_product_edit", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function editProduct(Request $request, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($request->get('product_id'));
        $this->denyAccessUnlessGranted('PRODUCT_MANAGE', $product);

        $productManagerData = new ProductManagerData();
        $productManagerData->extract($product);
        $productForm = $this->createForm(ProductManagerType::class, $productManagerData);
        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $productManagerData->fill($product);
            $this->getEm()->flush();
            $this->addFlash('success', "Товар {$product->getIdWithPrefix()} обновлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('manager/_product_edit_modal.html.twig', [
                'product' => $product,
                'productForm' => $productForm->createView(),
            ])
        ], 200);
    }

}
