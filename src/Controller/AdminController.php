<?php

namespace App\Controller;

use App\Entity\Package;
use App\Entity\Product;
use App\Resources\ItemCode;
use App\Services\ItemCodeParser;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends BaseController
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

    public function searchAction(Request $request, ItemCodeParser $itemCodeParser, $searchString)
    {
        try {
            $code = $itemCodeParser->parseString($searchString);
        } catch (Exception $e) {
            $code = null;
        }

        if ($code) {
            switch ($code->getType()) {
                case ItemCode::TYPE_ORDER:
                    return $this->renderOrder($code);
                    break;
                case ItemCode::TYPE_PACKAGE:
                    return $this->renderPackage($code);
                    break;
                case ItemCode::TYPE_PRODUCT:
                    return $this->renderProduct($code);
                    break;
                case ItemCode::TYPE_USER:
                    return $this->renderUser($code);
                    break;
                case ItemCode::TYPE_USER_ADDRESS:
                    return $this->renderUserAddress($code);
                    break;
                case ItemCode::TYPE_USER_PASSPORT:
                    return $this->renderUserPassport($code);
                    break;
            }
        } else {
            /* Search for package by tracking */
            $packages = $this->getEm()->getRepository(Package::class)->findBy(['tracking' => $searchString]);
            if ($packages) {
                return $this->renderPackage(reset($packages)->getItemCode());
            }
            /* Search for product by name */
            $products = $this->getEm()->getRepository(Product::class)->findBy(['name' => $searchString]);
            if ($products) {
                return $this->renderProduct($products);
            }

        }



        return $this->render('@Cargo/Dashboard/Info/notFound.html.twig', ['searchString' => $searchString]);
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
