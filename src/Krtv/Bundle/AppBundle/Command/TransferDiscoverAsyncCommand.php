<?php

namespace Krtv\Bundle\AppBundle\Command;

use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderBridgeAsyncInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TransferAsyncCommand
 * @package Krtv\Bundle\AppBundle\Command
 */
class TransferDiscoverAsyncCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('workflower:transfer:discover-async');
        $this->setDescription('Discover changes in connected async providers');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager  = $this->getContainer()->get('doctrine.orm.entity_manager');
        $bridgeRegistry = $this->getContainer()->get('app.provider.bridge_registry');

        $accounts = $entityManager->getRepository(ProviderAccount::class)->findAll();
        $accounts = array_filter($accounts, function (ProviderAccount $account) {
            return $account instanceof ProviderBridgeAsyncInterface;
        });

        while (true) {
            foreach ($accounts as $account) {
                $bridge = $bridgeRegistry->getBridge($account->getAsyncBridge());
                $bridge->execute($account);
            }

            usleep(200000);
        }

        return 0;
    }
} 