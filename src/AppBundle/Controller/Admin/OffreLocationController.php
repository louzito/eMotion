<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\OffreLocation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Offrelocation controller.
 *
 * @Route("manager/offrelocation")
 */
class OffreLocationController extends Controller
{
    /**
     * Lists all offreLocation entities.
     *
     * @Route("/", name="admin_offrelocation_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $offreLocations = $em->getRepository('AppBundle:OffreLocation')->findAll();

        return $this->render('admin/offrelocation/index.html.twig', array(
            'offreLocations' => $offreLocations,
        ));
    }

    /**
     * Creates a new offreLocation entity.
     *
     * @Route("/new", name="admin_offrelocation_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $offreLocation = new Offrelocation();
        $form = $this->createForm('AppBundle\Form\OffreLocationType', $offreLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($offreLocation);
            $em->flush();

            return $this->redirectToRoute('admin_offrelocation_show', array('id' => $offreLocation->getId()));
        }

        return $this->render('admin/offrelocation/new.html.twig', array(
            'offreLocation' => $offreLocation,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a offreLocation entity.
     *
     * @Route("/{id}", name="admin_offrelocation_show")
     * @Method("GET")
     */
    public function showAction(OffreLocation $offreLocation)
    {
        $deleteForm = $this->createDeleteForm($offreLocation);

        return $this->render('admin/offrelocation/show.html.twig', array(
            'offreLocation' => $offreLocation,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing offreLocation entity.
     *
     * @Route("/{id}/edit", name="admin_offrelocation_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, OffreLocation $offreLocation)
    {
        $deleteForm = $this->createDeleteForm($offreLocation);
        $editForm = $this->createForm('AppBundle\Form\OffreLocationType', $offreLocation);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_offrelocation_edit', array('id' => $offreLocation->getId()));
        }

        return $this->render('admin/offrelocation/edit.html.twig', array(
            'offreLocation' => $offreLocation,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a offreLocation entity.
     *
     * @Route("/{id}", name="admin_offrelocation_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, OffreLocation $offreLocation)
    {
        $form = $this->createDeleteForm($offreLocation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($offreLocation);
            $em->flush();
        }

        return $this->redirectToRoute('admin_offrelocation_index');
    }

    /**
     * Creates a form to delete a offreLocation entity.
     *
     * @param OffreLocation $offreLocation The offreLocation entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(OffreLocation $offreLocation)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_offrelocation_delete', array('id' => $offreLocation->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
