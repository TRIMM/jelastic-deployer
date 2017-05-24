<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/31/17
 * Time: 12:25 PM
 */

namespace Trimm\Jelastic\Service;


use Psr\Log\LoggerInterface;

class ConsoleService
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function log($message)
    {
        if ($this->logger) {
            $this->logger->info($message);
        }
    }
}