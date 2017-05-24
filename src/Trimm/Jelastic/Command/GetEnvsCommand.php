<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/26/17
 * Time: 2:46 PM
 */

namespace Trimm\Jelastic\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GetEnvsCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('jelastic:get-envs')
            ->setDescription('Get the environments.')
            ->setHelp('Retrieves the environments of the logged in user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Api */
        $api = $this->getContainer()->get('app.api');
        dump($api->getEnvs());
    }
}