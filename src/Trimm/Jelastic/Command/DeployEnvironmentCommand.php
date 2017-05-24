<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/26/17
 * Time: 2:46 PM
 */

namespace Trimm\Jelastic\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trimm\Jelastic\Service\Deployer;

class DeployEnvironmentCommand extends ConsoleLoggingCommand
{

    protected function configure()
    {
        $this
            ->setName('jelastic:deploy-env')
            ->setDescription('Deploy an environment.')
            ->setHelp('Deploy an environment, clones an existing environment if needed...')
            ->addArgument('branch', InputArgument::REQUIRED, 'The git branch of the project.')
            ->addArgument('prefix', InputArgument::OPTIONAL, 'The project prefix.')
        ;
    }

    protected function _execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Deployer */
        $deployer = $this->getContainer()->get('app.deployer');
        $this->setLogger($deployer);
        $deployer->deploy(
            $input->getArgument('branch'),
            $input->getArgument('prefix')
        );
    }
}