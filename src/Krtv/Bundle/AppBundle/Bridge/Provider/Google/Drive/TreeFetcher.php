<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider\Google\Drive;

use Krtv\Bundle\AppBundle\Model\Provider\Google\Entry\EntryModel;
use Krtv\Bundle\AppBundle\Model\Provider\Google\Entry\EntryTrashedModel;
use Krtv\Bundle\AppBundle\Model\Provider\Google\Entry\EntryWrapper;

/**
 * Class TreeFetcher
 * @package Krtv\Bundle\AppBundle\Bridge\Provider\Google\Drive
 */
class TreeFetcher
{
    /**
     * @param \Google_Service_Drive $service
     * @param array $items
     * @return array
     */
    public function fetch(\Google_Service_Drive $service, \Google_Client $client, array $items = [])
    {
        $client->setUseBatch(true);

        $treeData = [];
        $changes  = $items;

        do {
            $batch      = new \Google_Http_Batch($client);

            foreach ($items as $item) {
                if ($item instanceof \Google_Service_Drive_Change) {
                    if ($item->getDeleted() !== true && $item->getFile()->getLabels()->getTrashed() !== true) {
                        $entry   = new EntryModel($item->getFileId(), $item->getFile());
                        $parents = $item->getFile()->getParents();
                    } else {
                        $entry   = new EntryTrashedModel($item->getFileId());
                        $parents = [];
                    }

                    $entryWrapper = new EntryWrapper($entry, $parents);
                } elseif ($item instanceof \Google_Service_Drive_DriveFile) {
                    $entryWrapper = new EntryWrapper(new EntryModel($item->getId(), $item), $item->getParents());
                } else {
                    throw new \RuntimeException();
                }

                $treeData[$entryWrapper->getData()->getId()] = $entryWrapper;

                if ($entryWrapper->getData() instanceof EntryTrashedModel) {
                    continue;
                }

                foreach ($entryWrapper->getParents() as $treeParent) {
                    $request = $service->files->get($treeParent->getId());

                    $batch->add($request, $treeParent->getId());
                }
            }
        } while ($items = $batch->execute());

        foreach ($treeData as $id => $treeObject /** @var EntryWrapper $treeObject */) {
            $parents = $treeObject->getParents();

            foreach ($parents as $parentRef /** @var \Google_Service_Drive_ParentReference $treeParent */) {
                if (!isset($treeData[$parentRef->getId()])) {
                    continue;
                }

                /** @var EntryWrapper $treeParent */
                $treeParent = $treeData[$parentRef->getId()];

                $treeObject->getData()->addParent($treeParent->getData());
                $treeParent->getData()->addChild($treeObject->getData());
            }
        }

        $data = [];
        foreach ($changes as $change) {
            $entryId      = $change->getFileId();
            $entryWrapper = $treeData[$entryId];

            $data[$entryId] = $entryWrapper->getData();
        }

        $client->setUseBatch(false);

        return $data;
    }
} 