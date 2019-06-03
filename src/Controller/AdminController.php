<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_index", methods="GET")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return $this->render('admin/index.html.twig', []);
    }

    /**
     * @Route("/elements", name="admin_index", methods="GET")
     * @return Response
     */
    public function elements(): Response
    {
        return $this->render('admin/elements.html.twig', []);
    }

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
