<?php

namespace Devana\ShiftsBundle\Command;

use Devana\DoctrineBundle\DevanaDoctrineBundle;
use Doctrine\Tests\Common\Annotations\Fixtures\Controller;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Created by PhpStorm.
 * User: Paja
 * Date: 2.9.14.
 * Time: 13.17
 */

class ChangeStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('demand:change:state')->setDescription('Changes the state of obsolete demands');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $demands = $em->getRepository('DevanaDoctrineBundle:Demand')->findAll();

        $currentTime = new \DateTime();

        foreach($demands as $demand)
        {
            if($demand->getExpiresAt() < $currentTime)
            {
                $demand->setState("expired");
                $em->persist($demand);
            }
        }

        $em->flush();
    }
}
