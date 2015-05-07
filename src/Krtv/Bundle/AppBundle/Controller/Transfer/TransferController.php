<?php

namespace Krtv\Bundle\AppBundle\Controller\Transfer;

use Krtv\Bundle\AppBundle\Entity\Transfer;
use Krtv\Bundle\AppBundle\Form\Type\Transfer\TransferType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TransferController
 * @package Krtv\Bundle\AppBundle\Controller\Transfer
 */
class TransferController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new TransferType(), new Transfer());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();

            return new RedirectResponse($this->generateUrl('app_index'));
        }

        return $this->render('AppBundle:Transfer/Transfer:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Transfer $transfer
     * @param Request $request
     * @return RedirectResponse
     */
    public function syncAction(Transfer $transfer, Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $transferManager = $this->get('app.transfer_manager');
        $transferManager->discover($transfer);

        $entityManager->flush();

        return new RedirectResponse($this->generateUrl('app_index'));
    }
}
