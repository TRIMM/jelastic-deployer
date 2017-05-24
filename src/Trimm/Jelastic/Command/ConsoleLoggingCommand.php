<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/31/17
 * Time: 12:30 PM
 */

namespace Trimm\Jelastic\Command;


use Psr\Log\LogLevel;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Trimm\Jelastic\Service\ConsoleService;

abstract class ConsoleLoggingCommand extends ContainerAwareCommand
{
    protected $output;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->_execute($input, $output);
    }

    protected abstract function _execute(InputInterface $input, OutputInterface $output);

    protected function setLogger(ConsoleService $service)
    {
        $service->setLogger(new ConsoleLogger(
            $this->output,
            [
                LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL,
                LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL
            ]
        ));
    }
}