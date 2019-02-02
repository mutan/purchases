<?php

namespace App\Controller;

use App\Entity\UserAddress;
use App\Form\UserAddressType;
use App\Repository\UserAddressRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user/address")
 */
class UserAddressController extends AbstractController
{
    /**
     * @Route("/", name="user_address_index", methods={"GET"})
     */
    public function index(UserAddressRepository $userAddressRepository): Response
    {
        return $this->render('user_address/index.html.twig', [
            'user_addresses' => $userAddressRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_address_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $userAddress = new UserAddress();
        $form = $this->createForm(UserAddressType::class, $userAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($userAddress);
            $entityManager->flush();

            return $this->redirectToRoute('user_address_index');
        }

        return $this->render('user_address/new.html.twig', [
            'user_address' => $userAddress,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_address_show", methods={"GET"})
     */
    public function show(UserAddress $userAddress): Response
    {
        return $this->render('user_address/show.html.twig', [
            'user_address' => $userAddress,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_address_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, UserAddress $userAddress): Response
    {
        $form = $this->createForm(UserAddressType::class, $userAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_address_index', [
                'id' => $userAddress->getId(),
            ]);
        }

        return $this->render('user_address/edit.html.twig', [
            'user_address' => $userAddress,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_address_delete", methods={"DELETE"})
     */
    public function delete(Request $request, UserAddress $userAddress): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userAddress->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($userAddress);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_address_index');
    }
}