<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/profile")
 * @IsGranted("ROLE_USER")
 */
class UserController extends BaseController
{
    /**
     * @Route("/", name="user_profile")
     */
    public function index()
    {
        return $this->render('user_profile/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/edit", name="user_profile_edit", methods={"GET","POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('USER_PROFILE_EDIT', $this->getUser());

        $form = $this->createForm(UserProfileType::class, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getEm()->flush();
            $this->addFlash('success', "Профиль обновлен");
            $reload = true;
        }

        return new JsonResponse([
            'message' => 'Success',
            'reload' => $reload ?? false,
            'output' => $this->renderView('user_profile/_user_profile_modal.html.twig', [
                'user' => $this->getUser(),
                'form' => $form->createView(),
                'title' => 'Редактировать профиль'
            ])
        ], 200);
    }
}
