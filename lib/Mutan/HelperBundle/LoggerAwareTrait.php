<?php

namespace Mutan\HelperBundle;

use Psr\Log\LoggerInterface;

trait LoggerAwareTrait
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @required
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
