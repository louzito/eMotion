<?php

namespace AppBundle\Admin;

use AppBundle\Form\ImageType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class LocationAdmin extends AbstractAdmin
{

    // ajout
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('vehicule')
            ->add('prixJournalier')
            ->add('dateDebut')
            ->add('dateFin');
    }

    // filtre priority
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('vehicule');
    }

    // champs afficher sur listing
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('prixJournalier')->addIdentifier('vehicule')->add('dateDebut')->add('dateFin');
    }
}