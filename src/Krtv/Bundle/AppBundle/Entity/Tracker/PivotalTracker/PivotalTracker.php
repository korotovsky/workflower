<?php

namespace Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker;

use Doctrine\ORM\Mapping as ORM;
use Krtv\Bundle\AppBundle\Entity\Tracker;

/**
 * Class PivotalTracker
 * @package Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker
 *
 * @ORM\Entity()
 * @ORM\Table(name="tracker_type_pivotal")
 */
class PivotalTracker extends Tracker
{
    /**
     * @ORM\ManyToOne(targetEntity="Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker\PivotalTrackerAccount")
     */
    private $account;

    /**
     * @return PivotalTrackerAccount
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param PivotalTrackerAccount $account
     */
    public function setAccount(PivotalTrackerAccount $account)
    {
        $this->account = $account;
    }

    /**
     * @return string
     */
    public function getBridge()
    {
        return 'pivotal';
    }
}