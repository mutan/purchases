<?php

namespace Mutan\HelperBundle;

use Mutan\HelperBundle\DependencyInjection\MutanHelperExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MutanHelperBundle extends Bundle
{
    /* This is needed to use alias from MutanHelperExtension */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new MutanHelperExtension();
        }

        return $this->extension;
    }
}
