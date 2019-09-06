<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Package;
use App\Entity\Product;
use App\Repository\UserRepository;
use App\Resources\ItemCode;
use App\Services\ItemCodeParser;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * @Route("/", name="admin_search", methods="POST")
     * @param Request $request
     * @param ItemCodeParser $itemCodeParser
     * @return Response
     */
    public function search(Request $request, ItemCodeParser $itemCodeParser): Response
    {
        $searchString = $request->request->get('search_item');
        try {
            $code = $itemCodeParser->parseString($searchString);
        } catch (Exception $e) {
            $code = null;
        }

        $template = 'admin/index.html.twig';
        $params = [
            'search_string' => $searchString,
            'code' => $code,
        ];

        if ($code) {
            switch ($code->getType()) {
                case ItemCode::TYPE_ORDER:
                    $order = $this->getEm()->getRepository(Order::class)->find($code->getNumber());
                    $params['order'] = $order;
                    $template = 'admin/order.html.twig';
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

        return $this->render($template, $params);
    }

    /**
     * @Route("/users", name="admin_user_list", methods="GET")
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     */
    public function users(UserRepository $userRepository, Request $request): Response
    {
        $search = $request->query->get('search');
        $users  = $userRepository->findAllWithSearch($search);

        return $this->render('admin/user_list.html.twig', [
            'users' => $users,
        ]);
    }
}
