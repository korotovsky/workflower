<?php

namespace Krtv\Bundle\AppBundle\Controller;

use Krtv\Bundle\AppBundle\Entity\ProviderAccount;
use Krtv\Bundle\AppBundle\Entity\Transfer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class IndexController
 * @package Krtv\Bundle\AppBundle\Controller
 */
class IndexController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $providers = $this->getDoctrine()->getRepository(ProviderAccount::class)->findAll();
        $transfers = $this->getDoctrine()->getRepository(Transfer::class)->findAll();

        return $this->render('AppBundle:Index:index.html.twig', [
            'providers' => $providers,
            'transfers' => $transfers,
        ]);
    }
}
