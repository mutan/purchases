<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     * @param Request $resuest
     * @param $debugLogFile
     */
    public function index(Request $resuest, $debugLogFile)
    {
        # see log at var\log\debug.log
        file_put_contents($debugLogFile, print_r('test', true));

        throw $this->createNotFoundException('Test');
     }
}
