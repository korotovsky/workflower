<?php

namespace Krtv\Bundle\AppBundle\Entity\Provider\Dropbox;

use Doctrine\ORM\Mapping as ORM;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\ProviderBridgeInterface;

/**
 * Class DropboxAccount
 * @package Krtv\Bundle\AppBundle\Entity\Provider\Dropbox
 *
 * @ORM\Entity()
 * @ORM\Table(name="account_provider_dropboxes")
 */
class DropboxAccount extends ProviderAccount implements ProviderBridgeInterface
{
    /**
     * @return string
     */
    public function getBridge()
    {
        return 'dropbox';
    }
}
