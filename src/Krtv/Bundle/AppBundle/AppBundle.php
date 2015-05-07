<?php

namespace Krtv\Bundle\AppBundle;

use Krtv\Bundle\AppBundle\DependencyInjection\KrtvAppExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AppBundle
 * @package Krtv\Bundle\AppBundle
 */
class AppBundle extends Bundle
{
    /**
     * @return KrtvAppExtension|null|\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
     */
    public function getContainerExtension()
    {
        if ($this->extension === null) {
            $this->extension = new KrtvAppExtension();
        }

        return $this->extension;
    }

}
