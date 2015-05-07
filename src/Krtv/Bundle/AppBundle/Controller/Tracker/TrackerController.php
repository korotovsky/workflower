<?php

namespace Krtv\Bundle\AppBundle\Controller\Tracker;

use Krtv\Bundle\AppBundle\Entity\PivotalTracker\PivotalProject;
use Krtv\Bundle\AppBundle\Entity\Tracker\PivotalTracker\PivotalTracker;
use Krtv\Bundle\AppBundle\Form\Type\PivotalTracker\ProjectType;
use Krtv\Bundle\AppBundle\Form\Type\Tracker\TrackerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TrackerController
 * @package Krtv\Bundle\AppBundle\Controller\PivotalTracker
 */
class TrackerController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(new TrackerType(), new PivotalTracker());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();

            return new RedirectResponse($this->generateUrl('app_index'));
        }

        return $this->render('AppBundle:PivotalTracker/Project:create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
