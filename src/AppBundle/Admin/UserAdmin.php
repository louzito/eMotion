<?php

namespace AppBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends AbstractAdmin
{

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('username','text')->add('password', 'password')->add('email', 'text')
            ->add('nom')
            ->add('prenom')
            ->add('dateDeNaissance')
            ->add('adresse')
            ->add('numPermis')
            ->add('telephone')
            ->add('ville')
            ->add('cp')
            ->add('roles')->add('enabled');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('username')->addIdentifier('email');
    }
}