<?php
/**
 * Created by PhpStorm.
 * User: mwienk
 * Date: 1/26/17
 * Time: 12:19 PM
 */

namespace Trimm\Jelastic\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Trimm\Jelastic\Service\Api;

class SignInCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('jelastic:signin')
            ->setDescription('Sign in the user.')
            ->setHelp('This command allows you to sign in as a user...')
            ->addArgument('username', InputArgument::REQUIRED, 'The email address used to login.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password used to login.')
            ->addArgument('platformUrl', InputArgument::REQUIRED, 'The Jelastic platform url.')
        ;

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var Api */
        $api = $this->getContainer()->get('app.api');
        $output->writeln($api->signin(
            $input->getArgument('username'),
            $input->getArgument('password'),
            $input->getArgument('platformUrl')
        ));
    }

}