<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider;

use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Model\Provider\EntryModelInterface;

/**
 * Interface BridgeDeltaInterface
 * @package Krtv\Bundle\AppBundle\Bridge\Provider
 */
interface BridgeDeltaInterface extends BridgeInterface
{
    /**
     * @param ProviderAccount $account
     * @param null|string     $cursor
     * @return mixed
     */
    public function getDelta(ProviderAccount $account, $cursor = null);

    /**
     * @param ProviderAccount     $account
     * @param EntryModelInterface $entry
     * @return string
     */
    public function getSharedUrl(ProviderAccount $account, EntryModelInterface $entry);

    /**
     * @param ProviderAccount     $account
     * @param EntryModelInterface $entry
     * @return resource
     */
    public function getFile(ProviderAccount $account, EntryModelInterface $entry);
} 