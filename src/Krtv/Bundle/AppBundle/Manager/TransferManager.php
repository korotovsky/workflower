<?php

namespace Krtv\Bundle\AppBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Krtv\Bundle\AppBundle\Bridge\Provider\BridgeRegistry;
use Krtv\Bundle\AppBundle\Entity\Transfer;
use Krtv\Bundle\AppBundle\Entity\Transfer\ChangeMessage;

/**
 * Class TransferManager
 * @package Krtv\Bundle\AppBundle\Manager
 */
class TransferManager
{
    /**
     * @var BridgeRegistry
     */
    private $bridgeRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param BridgeRegistry $bridgeRegistry
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(BridgeRegistry $bridgeRegistry, EntityManagerInterface $entityManager)
    {
        $this->bridgeRegistry = $bridgeRegistry;
        $this->entityManager  = $entityManager;
    }

    /**
     * @param Transfer $transfer
     * @return array
     */
    public function discover(Transfer $transfer)
    {
        $changes = [];

        foreach ($transfer->getProviders() as $provider) {
            $bridge = $this->bridgeRegistry->getBridge($provider->getBridge());
            $cursor = $provider->getCursor();

            do {
                $delta  = $bridge->getDelta($provider, $cursor);
                $cursor = $delta->getCursor();

                $provider->setCursor($cursor);

                foreach ($delta->getEntries()->groupBy() as $collection) {
                    foreach ($collection as $id => $entry) {
                        $change = new ChangeMessage();
                        $change->setState(ChangeMessage::STATE_SCHEDULED);
                        $change->setProvider($provider);
                        $change->setTracker($transfer->getTracker());
                        $change->setData($entry);

                        $changes[] = $change;

                        $this->entityManager->persist($change);
                    }
                }
            } while ($delta->hasMore());
        }

        return $changes;
    }
} 