<?php

namespace Krtv\Bundle\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class ProviderAccount
 * @package Krtv\Bundle\AppBundle\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="accounts")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="account_type", type="string")
 * @ORM\DiscriminatorMap({
 *    "dropbox" ="Krtv\Bundle\AppBundle\Entity\Provider\Dropbox\DropboxAccount",
 *    "yandex"  ="Krtv\Bundle\AppBundle\Entity\Provider\Yandex\YandexAccount",
 *    "google"  ="Krtv\Bundle\AppBundle\Entity\Provider\Google\GoogleAccount"
 * })
 */
abstract class ProviderAccount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $uid;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=1024, nullable=true)
     */
    private $cursor;

    /**
     * @ORM\OneToMany(targetEntity="Krtv\Bundle\AppBundle\Entity\ProviderAccountToken", mappedBy="account", orphanRemoval=true, fetch="EXTRA_LAZY")
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $tokens;

    /**
     *
     */
    public function __construct()
    {
        $this->tokens = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ProviderAccountToken
     */
    public function getToken()
    {
        return $this->tokens->first();
    }

    /**
     * @param ProviderAccountToken $token
     */
    public function addToken(ProviderAccountToken $token)
    {
        $this->tokens->add($token);
    }

    /**
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param int $uid
     */
    public function setUid($uid)
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * @param string $cursor
     */
    public function setCursor($cursor)
    {
        $this->cursor = $cursor;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s@%s', $this->getName(), $this->getUid());
    }
} 