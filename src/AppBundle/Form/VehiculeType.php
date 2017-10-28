<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class VehiculeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('marque')
            ->add('modele')
            ->add('numSerie')
            ->add('couleur')
            ->add('plaqueImmatriculation')
            ->add('nbKilometres')
            ->add('dateAchat', 'date' ,array(
                'label' => 'Date d\'achat (dd/mm/yyyy)',
                'widget'=> 'single_text',
                'format'=>'dd/MM/yyyy'))
            ->add('type', ChoiceType::class, array(
                'choices'  => array(
                    'Voiture' => 'Voiture',
                    'Scooter' => 'Scooter',
                ),
            ))
            ->add('prixAchat')
            ->add('image', ImageType::class, [
                    'required' => false,
                ])
            ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Vehicule'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_vehicule';
    }


}
