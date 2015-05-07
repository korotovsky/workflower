<?php

namespace Krtv\Bundle\AppBundle\Command;

use Krtv\Bundle\AppBundle\Bridge\Provider\BridgeDeltaInterface;
use Krtv\Bundle\AppBundle\Bridge\Tracker\PivotalTracker\Pivotal;
use Krtv\Bundle\AppBundle\Entity\Transfer\ChangeMessage;
use Krtv\Bundle\AppBundle\Exception\ProviderException;
use Krtv\Bundle\AppBundle\Exception\TrackerException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class TransferChangesCommand
 * @package Krtv\Bundle\AppBundle\Command
 */
class TransferChangesCommand extends ContainerAwareCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this->setName('workflower:transfer:sync');
        $this->setDescription('Process transfer changes from providers to PivotalTracker');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $entityManager  = $this->getContainer()->get('doctrine.orm.entity_manager');
        $providerBridgeRegistry = $this->getContainer()->get('app.provider.bridge_registry');
        $trackerBridgeRegistry  = $this->getContainer()->get('app.tracker.bridge_registry');

        $changes = $entityManager->getRepository(ChangeMessage::class)->findBy([
            'state' => ChangeMessage::STATE_SCHEDULED,
        ], [], 10);

        if (count($changes) === 0) {
            return 0;
        }

        $entityManager->transactional(function () use ($changes) {
            foreach ($changes as $change) {
                $change->setState(ChangeMessage::STATE_IN_PROGRESS);
            }
        });

        foreach ($changes as $change) {
            $provider = $change->getProvider();
            $tracker  = $change->getTracker();
            $entry    = $change->getData();
            $story    = null;

            if ($entry->isDirectory()) {
                $change->setState(ChangeMessage::STATE_COMPLETED);

                continue;
            }

            /** @var BridgeDeltaInterface $providerBridge */
            $providerBridge = $providerBridgeRegistry->getBridge($provider->getBridge());
            /** @var Pivotal $trackerBridge */
            $trackerBridge  = $trackerBridgeRegistry->getBridge($tracker->getBridge());

            // Ensure that matched story exists in tracker, otherwise it's false positive match
            try {
                $story = $trackerBridge->getStory($tracker, $entry->match());
            } catch (TrackerException $e) {
                $change->setState(ChangeMessage::STATE_ERROR);

                continue;
            }

            $text = null;
            $file = null;

            try {
                $text = $providerBridge->getSharedUrl($provider, $entry);
                $text = sprintf('%s @ %s', $text, $entry->getBaseName());
            } catch (ProviderException $e) {
                $change->setState(ChangeMessage::STATE_ERROR);

                continue;
            }

            try {
                $stream = null;
                if ($entry->getBytes() <= 10000000) {
                    $stream = $providerBridge->getFile($provider, $entry);
                }

                try {
                    if ($stream !== null) {
                        $file = $trackerBridge->addFile($tracker, $entry, $stream);
                    }
                } catch (TrackerException $e) {}
            } catch (ProviderException $e) { }

            try {
                $trackerBridge->addComment($tracker, $story, $entry, $file, $text);

                $change->setState(ChangeMessage::STATE_COMPLETED);
            } catch (TrackerException $e) {
                $change->setState(ChangeMessage::STATE_ERROR);
            }
        }

        $entityManager->flush();

        return 0;
    }
} 