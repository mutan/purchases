<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/users", name="user_list", methods="GET")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    public function users(UserRepository $userRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        $users  = $userRepository->findAllWithSearch($search);

        return $this->render('admin/user_list.html.twig', [
            'users'      => $users,
        ]);
    }

}
