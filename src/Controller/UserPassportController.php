<?php

namespace App\Controller;

use App\Entity\UserPassport;
use App\Form\UserPassportType;
use App\Repository\UserPassportRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/passport")
 * @IsGranted("ROLE_USER")
 */
class UserPassportController extends AbstractController
{
    /**
     * @Route("/", name="user_passport_index", methods={"GET"})
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
     */
    public function new(Request $request): Response
    {
        $userPassport = new UserPassport();
        $form = $this->createForm(UserPassportType::class, $userPassport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userPassport->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userPassport);
            $entityManager->flush();

            return $this->redirectToRoute('user_passport_index');
        }

        return $this->render('user_passport/new.html.twig', [
            'user_passport' => $userPassport,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_passport_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserPassport $userPassport): Response
    {
        $this->denyAccessUnlessGranted('USER_PASSPORT_MANAGE', $userPassport);

        $form = $this->createForm(UserPassportType::class, $userPassport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_passport_index', [
                'id' => $userPassport->getId(),
            ]);
        }

        return $this->render('user_passport/edit.html.twig');
    }

    /**
     * @Route("/{id}", name="user_passport_delete", methods={"DELETE"})
     */
    public function delete(Request $request, UserPassport $userPassport): Response
    {
        $this->denyAccessUnlessGranted('USER_PASSPORT_MANAGE', $userPassport);

        if ($this->isCsrfTokenValid('delete'.$userPassport->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $userPassport->setStatus($userPassport::STATUS_DELETED);
            $entityManager->persist($userPassport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_passport_index');
    }
}
