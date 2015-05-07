<?php

namespace Krtv\Bundle\AppBundle\Debounce;

use Krtv\Bundle\AppBundle\Entity\ProviderAccount;

/**
 * Interface DebounceInterface
 * @package Krtv\Bundle\AppBundle\Debounce
 */
interface DebounceInterface
{
    /**
     * @param ProviderAccount $provider
     * @param string $id
     * @param string $hash
     * @return mixed
     */
    public function debounce(ProviderAccount $provider, $id, $hash);
} 