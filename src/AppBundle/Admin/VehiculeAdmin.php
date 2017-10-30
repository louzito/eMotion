<?php

namespace AppBundle\Admin;

use AppBundle\Form\ImageType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class VehiculeAdmin extends AbstractAdmin
{

    // ajout
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('marque')
            ->add('modele')
            ->add('numSerie')
            ->add('couleur')
            ->add('plaqueImmatriculation')
            ->add('nbKilometres')
            ->add('dateAchat')
            ->add('prixAchat')
            ->add('image', ImageType::class, [
                'required' => false,
            ]);
    }

    // filtre priority
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('marque');
    }

    // champs afficher sur listing
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('marque')->addIdentifier('numSerie')->add('plaqueImmatriculation');
    }
}