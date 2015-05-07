<?php

namespace Krtv\Bundle\AppBundle\Command;

use Krtv\Bundle\AppBundle\Entity\Transfer;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TransferDiscoverCommand
 * @package Krtv\Bundle\AppBundle\Command
 */
class TransferDiscoverCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('workflower:transfer:discover');
        $this->setDescription('Discover changes in connected providers');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager   = $this->getContainer()->get('doctrine.orm.entity_manager');
        $transferManager = $this->getContainer()->get('app.transfer_manager');

        $transfers = $entityManager->getRepository(Transfer::class)->findAll();

        foreach ($transfers as $transfer) {
            $transferManager->discover($transfer);
        }

        $entityManager->flush();

        return 0;
    }
} 