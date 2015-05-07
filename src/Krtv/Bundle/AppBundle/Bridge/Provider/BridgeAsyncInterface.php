<?php

namespace Krtv\Bundle\AppBundle\Bridge\Provider;

use Krtv\Bundle\AppBundle\Entity\ProviderAccount;

/**
 * Interface BridgeAsyncInterface
 * @package Krtv\Bundle\AppBundle\Bridge\Provider
 */
interface BridgeAsyncInterface extends BridgeInterface
{
    /**
     * @param ProviderAccount $account
     * @return mixed
     */
    public function execute(ProviderAccount $account);
} 