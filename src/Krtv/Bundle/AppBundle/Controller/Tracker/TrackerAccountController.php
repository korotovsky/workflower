<?php

namespace Krtv\Bundle\AppBundle\Controller\Tracker;

use Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker\PivotalTrackerAccount;
use Krtv\Bundle\AppBundle\Form\Type\Tracker\TrackerAccountType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrackerAccountController
 * @package Krtv\Bundle\AppBundle\Controller\Tracker
 */
class TrackerAccountController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new TrackerAccountType(), new PivotalTrackerAccount());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();

            return new RedirectResponse($this->generateUrl('app_index'));
        }

        return $this->render('AppBundle:PivotalTracker/Account:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
