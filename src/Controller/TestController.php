<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function index(Request $resuest, $debugLogPath, $debugLogFile)
    {
        # see log at var\log\debug.log
        file_put_contents($debugLogPath . DIRECTORY_SEPARATOR . $debugLogFile, print_r('test', true));

        throw $this->createNotFoundException('Test');
     }
}
