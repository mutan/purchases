<?php

namespace App\Controller;

use App\Entity\Basket;
use App\Form\BasketManagerData;
use App\Form\BasketManagerType;
use App\Form\ProductManagerData;
use App\Form\ProductManagerType;
use App\Repository\BasketRepository;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @IsGranted("ROLE_MANAGER")
 */
class ManagerController extends BaseController
{
    /**
     * Страница со списком заказов менеджера
     * @Route("/manager/baskets", name="manager_basket_list", methods={"GET"})
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
     * Форма редактирования заказа менеджером (ajax)
     * @Route("/manager/basket/{basket_id}/edit", name="manager_basket_edit", methods={"POST"})
     * @param Request $request
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function editBasket(Request $request, BasketRepository $basketRepository) : Response
    {
        $basket = $basketRepository->findWithRelations($request->get('basket_id'));
        $this->denyAccessUnlessGranted('BASKET_MANAGE', $basket);

        $basketManagerData = new BasketManagerData();
        $basketManagerData->extract($basket);
        $basketForm = $this->createForm(BasketManagerType::class, $basketManagerData);
        $basketForm->handleRequest($request);

        if ($basketForm->isSubmitted() && $basketForm->isValid()) {
            $basketManagerData->fill($basket);
            $this->getEm()->flush();
            $this->addFlash('success', "Заказ {$basket->getIdWithPrefix()} обновлен.");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('manager/_basket_edit_modal.html.twig', [
                'basket' => $basket,
                'basketForm' => $basketForm->createView(),
            ])
        ], 200);
    }

    /**
     * Страница с одним заказом и списком продуктов для менеджера
     * @Route("/manager/basket/{basket_id}", name="manager_basket_show", methods={"GET"})
     * @param Request $request
     * @param BasketRepository $basketRepository
     * @return Response
     */
    public function show(Request $request, BasketRepository $basketRepository): Response
    {
        $basket = $basketRepository->findWithRelations($request->get('basket_id'));
        $this->denyAccessUnlessGranted('BASKET_MANAGE', $basket);

        return $this->render('manager/basket_show.html.twig', [
            'basket' => $basket,
        ]);
    }

    /**
     * Форма редактирования товара для менеджера (ajax)
     * @Route("manager/product/{product_id}/edit", name="manager_product_edit", methods={"POST"})
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
