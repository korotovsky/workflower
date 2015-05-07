<?php

namespace Krtv\Bundle\AppBundle\Entity\Provider\Google;

use Doctrine\ORM\Mapping as ORM;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderBridgeInterface;

/**
 * Class GoogleAccount
 * @package Krtv\Bundle\AppBundle\Entity\Provider\Google
 *
 * @ORM\Entity()
 * @ORM\Table(name="account_provider_googles")
 */
class GoogleAccount extends ProviderAccount implements ProviderBridgeInterface
{
    /**
     * @return string
     */
    public function getBridge()
    {
        return 'google';
    }
}
