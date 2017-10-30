<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Vehicule;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Vehicule controller.
 *
 * @Route("manager/vehicule")
 */
class VehiculeController extends Controller
{
    /**
     * Lists all vehicule entities.
     *
     * @Route("/", name="admin_vehicule_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $typeTrie = ($request->query->get('type-trie')) ? $request->query->get('type-trie') : null;
        if(!is_null($typeTrie) && ($typeTrie == "voiture" || $typeTrie == "scooter")){
            $vehicules = $em->getRepository('AppBundle:Vehicule')->findBy(array('type' => $typeTrie));
        }else{
            $vehicules = $em->getRepository('AppBundle:Vehicule')->findAll();
        }

        return $this->render('admin/vehicule/index.html.twig', array(
            'vehicules' => $vehicules,
            'typeTrie' => $typeTrie,
        ));
    }

    /**
     * Creates a new vehicule entity.
     *
     * @Route("/new", name="admin_vehicule_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $vehicule = new Vehicule();
        $form = $this->createForm('AppBundle\Form\VehiculeType', $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vehicule);
            $em->flush();

            return $this->redirectToRoute('admin_vehicule_show', array('id' => $vehicule->getId()));
        }

        return $this->render('admin/vehicule/new.html.twig', array(
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a vehicule entity.
     *
     * @Route("/{id}", name="admin_vehicule_show")
     * @Method("GET")
     */
    public function showAction(Vehicule $vehicule)
    {
        $deleteForm = $this->createDeleteForm($vehicule);

        return $this->render('admin/vehicule/show.html.twig', array(
            'vehicule' => $vehicule,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing vehicule entity.
     *
     * @Route("/{id}/edit", name="admin_vehicule_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Vehicule $vehicule)
    {
        $deleteForm = $this->createDeleteForm($vehicule);
        $editForm = $this->createForm('AppBundle\Form\VehiculeType', $vehicule);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_vehicule_edit', array('id' => $vehicule->getId()));
        }

        return $this->render('admin/vehicule/edit.html.twig', array(
            'vehicule' => $vehicule,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a vehicule entity.
     *
     * @Route("/{id}", name="admin_vehicule_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Vehicule $vehicule)
    {
        $form = $this->createDeleteForm($vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vehicule);
            $em->flush();
        }

        return $this->redirectToRoute('admin_vehicule_index');
    }

    /**
     * Creates a form to delete a vehicule entity.
     *
     * @param Vehicule $vehicule The vehicule entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Vehicule $vehicule)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_vehicule_delete', array('id' => $vehicule->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
