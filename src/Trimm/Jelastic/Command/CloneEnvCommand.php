<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/26/17
 * Time: 2:46 PM
 */

namespace Trimm\Jelastic\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CloneEnvCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('jelastic:clone-env')
            ->setDescription('Clone an environment.')
            ->setHelp('Clone an existing environment...')
            ->addArgument('appid', InputArgument::REQUIRED, 'The id of the application to clone.')
            ->addArgument('domain', InputArgument::REQUIRED, 'The domain of the new environment.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Api */
        $api = $this->getContainer()->get('app.api');
        dump($api->cloneEnv(
            $input->getArgument('appid'),
            $input->getArgument('domain')
        ));
    }
}