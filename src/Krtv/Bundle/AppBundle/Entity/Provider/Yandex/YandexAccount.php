<?php

namespace Krtv\Bundle\AppBundle\Entity\Provider\Yandex;

use Doctrine\ORM\Mapping as ORM;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderBridgeAsyncInterface;
use Krtv\Bundle\AppBundle\Entity\ProviderBridgeInterface;

/**
 * Class YandexAccount
 * @package Krtv\Bundle\AppBundle\Entity\Provider\Yandex
 *
 * @ORM\Entity()
 * @ORM\Table(name="account_provider_yandexes")
 */
class YandexAccount extends ProviderAccount implements ProviderBridgeInterface, ProviderBridgeAsyncInterface
{
    /**
     * @return string
     */
    public function getBridge()
    {
        return 'yandex';
    }

    /**
     * @return string
     */
    public function getAsyncBridge()
    {
        return 'yandex_xmpp';
    }
}
