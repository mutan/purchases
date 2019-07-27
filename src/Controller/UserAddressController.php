<?php

namespace App\Controller;

use App\Entity\UserAddress;
use App\Form\UserAddressType;
use App\Repository\UserAddressRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/address")
 * @IsGranted("ROLE_USER")
 */
class UserAddressController extends BaseController
{
    /**
     * @Route("/", name="user_address_index", methods={"GET"})
     * @param UserAddressRepository $userAddressRepository
     * @return Response
     */
    public function index(UserAddressRepository $userAddressRepository): Response
    {
        $user = $this->getUser();

        return $this->render('user_address/index.html.twig', [
            'user_addresses' => $userAddressRepository->findAllByUser($user),
        ]);
    }

    /**
     * @Route("/new", name="user_address_new", methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function new(Request $request): JsonResponse
    {
        $userAddress = new UserAddress();
        $form = $this->createForm(UserAddressType::class, $userAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userAddress->setUser($this->getUser());
            $this->getEm()->persist($userAddress);
            $this->getEm()->flush();
            $this->addFlash('success', "Адрес добавлен");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('user_address/_user_address_modal.html.twig', [
                'form' => $form->createView(),
                'title' => 'Новый адрес'
            ])
        ], 200);
    }

    /**
     * @Route("/{id}/edit", name="user_address_edit", methods={"GET","POST"})
     * @param Request $request
     * @param UserAddress $userAddress
     * @return JsonResponse
     */
    public function edit(Request $request, UserAddress $userAddress): JsonResponse
    {
        $this->denyAccessUnlessGranted('USER_ADDRESS_EDIT', $userAddress);

        $form = $this->createForm(UserAddressType::class, $userAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', "Адрес обновлен");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('user_address/_user_address_modal.html.twig', [
                'user_address' => $userAddress,
                'form' => $form->createView(),
                'title' => 'Редактировать адрес'
            ])
        ], 200);
    }

    /**
     * @Route("/{id}", name="user_address_delete", methods={"DELETE"})
     * @param Request $request
     * @param UserAddress $userAddress
     * @return Response
     */
    public function delete(Request $request, UserAddress $userAddress): Response
    {
        $this->denyAccessUnlessGranted('USER_ADDRESS_EDIT', $userAddress);

        if (!$userAddress->isNew()) {
            $this->addFlash('danger', "Статус адреса {$userAddress->getIdWithPrefix()} – {$userAddress->getStatus()}. Удалить можно только адрес в статусе New.");
            return $this->redirectToRoute('user_address_index');
        }

        if ($this->isCsrfTokenValid('delete'.$userAddress->getId(), $request->request->get('_token'))) {
            $userAddress->setStatus(UserAddress::STATUS_DELETED);
            $this->getEm()->persist($userAddress);
            $this->getEm()->flush();
            $this->addFlash('danger', "Адрес {$userAddress->getIdWithPrefix()} удален.");
        }

        return $this->redirectToRoute('user_address_index');
    }
}
