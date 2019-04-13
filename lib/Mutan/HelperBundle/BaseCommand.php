<?php

namespace Mutan\HelperBundle;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class BaseCommand extends Command implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface|null
     */
    private $container;


    protected function getContainer()
    {
        if (null === $this->container) {
            $application = $this->getApplication();
            if (null === $application) {
                throw new \LogicException('The container cannot be retrieved as the application instance is not yet set.');
            }

            $this->container = $application->getKernel()->getContainer();
        }

        return $this->container;
    }


    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }




    public function getDoctrine()
    {
        if (!$this->getContainer()->has('doctrine')) {
            throw new \LogicException('The DoctrineBundle is not registered in your application.');
        }
        return $this->getContainer()->get('doctrine');
    }

    /**
     * @return ObjectManager|EntityManager|object
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * @return Logger|object
     */
    protected function getLogger()
    {
        if (!$this->getContainer()->has('logger')) {
            throw new \LogicException('The MonologBundle is not registered in your application.');
        }
        return $this->getContainer()->get('logger');
    }
}
