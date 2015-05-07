<?php

namespace Krtv\Bundle\AppBundle\Debounce;

use Doctrine\ORM\EntityManagerInterface;
use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\Transfer\BounceMessage;

/**
 * Service to detect repeated changes from provider
 *
 * Class Debounce
 * @package Krtv\Bundle\AppBundle\Debounce
 */
class Debounce implements DebounceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * The method returns false if bounce detected
     *
     * @param ProviderAccount $provider
     * @param string $id
     * @param string $hash
     * @return bool|void
     */
    public function debounce(ProviderAccount $provider, $id, $hash)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('m')
            ->from(BounceMessage::class, 'm')
            ->andWhere('m.identifier = :identifier')
                ->setParameter('identifier', $id)
            ->andWhere('m.hash = :hash')
                ->setParameter('hash', $hash)
            ->andWhere('m.expireAt > :now')
            ->setParameter('now', (new \DateTime())->format(\DateTime::ISO8601));

        $row = $qb->getQuery()->getOneOrNullResult();
        if ($row !== null) {
            return false;
        }

        $bounce = new BounceMessage();
        $bounce->setProvider($provider);
        $bounce->setIdentifier($id);
        $bounce->setHash($hash);

        $this->entityManager->persist($bounce);
        $this->entityManager->flush();

        return true;
    }
}