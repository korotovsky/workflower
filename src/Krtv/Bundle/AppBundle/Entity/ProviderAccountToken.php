<?php

namespace Krtv\Bundle\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProviderAccountToken
 * @package Krtv\Bundle\AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="account_tokens")
 */
class ProviderAccountToken
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $accessToken;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $refreshToken;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $lifetime;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="Krtv\Bundle\AppBundle\Entity\ProviderAccount", inversedBy="tokens")
     */
    private $account;

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param string $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param string $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return int
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * @param int $lifetime
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param ProviderAccount $account
     */
    public function setAccount(ProviderAccount $account)
    {
        $this->account = $account;
    }
}
