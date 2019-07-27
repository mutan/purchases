<?php

namespace App\Controller;

use App\Entity\UserPassport;
use App\Form\UserPassportType;
use App\Repository\UserPassportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/passport")
 * @IsGranted("ROLE_USER")
 */
class UserPassportController extends BaseController
{
    /**
     * @Route("/", name="user_passport_index", methods={"GET"})
     * @param UserPassportRepository $userPassportRepository
     * @return Response
     */
    public function index(UserPassportRepository $userPassportRepository): Response
    {
        $user = $this->getUser();

        return $this->render('user_passport/index.html.twig', [
            'user_passports' => $userPassportRepository->findAllByUser($user),
        ]);
    }

    /**
     * @Route("/new", name="user_passport_new", methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function new(Request $request): JsonResponse
    {
        $userPassport = new UserPassport();
        $form = $this->createForm(UserPassportType::class, $userPassport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userPassport->setUser($this->getUser());
            $this->getEm()->persist($userPassport);
            $this->getEm()->flush();
            $this->addFlash('success', "Паспорт добавлен");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('user_passport/_user_passport_modal.html.twig', [
                'form' => $form->createView(),
                'title' => 'Новый паспорт'
            ])
        ], 200);
    }

    /**
     * @Route("/{id}/edit", name="user_passport_edit", methods={"GET","POST"})
     * @param Request $request
     * @param UserPassport $userPassport
     * @return JsonResponse
     */
    public function edit(Request $request, UserPassport $userPassport): JsonResponse
    {
        $this->denyAccessUnlessGranted('USER_PASSPORT_EDIT', $userPassport);

        $form = $this->createForm(UserPassportType::class, $userPassport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEm()->flush();
            $this->addFlash('success', "Паспорт обновлен");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('user_passport/_user_passport_modal.html.twig', [
                'user_passport' => $userPassport,
                'form' => $form->createView(),
                'title' => 'Редактировать паспорт'
            ])
        ], 200);
    }

    /**
     * @Route("/{id}", name="user_passport_delete", methods={"DELETE"})
     * @param Request $request
     * @param UserPassport $userPassport
     * @return Response
     */
    public function delete(Request $request, UserPassport $userPassport): Response
    {
        $this->denyAccessUnlessGranted('USER_PASSPORT_EDIT', $userPassport);

        if (!$userPassport->isNew()) {
            $this->addFlash('danger', "Статус паспорта {$userPassport->getIdWithPrefix()} – {$userPassport->getStatus()}. Удалить можно только паспорт в статусе New");
            return $this->redirectToRoute('user_passport_index');
        }

        if ($this->isCsrfTokenValid('delete'.$userPassport->getId(), $request->request->get('_token'))) {
            $userPassport->setStatus(UserPassport::STATUS_DELETED);
            $this->getEm()->persist($userPassport);
            $this->getEm()->flush();
            $this->addFlash('danger', "Паспорт {$userPassport->getIdWithPrefix()} удален.");
        }

        return $this->redirectToRoute('user_passport_index');
    }
}
