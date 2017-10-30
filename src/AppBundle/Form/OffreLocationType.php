<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class OffreLocationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', 'date' ,array(
                'label' => 'Date de début (dd/mm/yyyy)',
                'widget'=> 'single_text',
                'format'=>'dd/MM/yyyy'))
            ->add('dateFin', 'date' ,array(
                'label' => 'Date de fin (dd/mm/yyyy)',
                'widget'=> 'single_text',
                'format'=>'dd/MM/yyyy'))
            ->add('prixJournalier')
            ->add('vehicule');
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\OffreLocation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_offrelocation';
    }


}
