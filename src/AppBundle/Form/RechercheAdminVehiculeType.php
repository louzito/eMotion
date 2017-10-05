<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class RechercheAdminVehiculeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', EntityType::class, array(
                    'class' => 'AppBundle:User',
                    'choice_label' => 'prenomNomNumPermis',
            ))
            ->add('dateDebut', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'Date de Début',
                    ],
            ))
            ->add('dateFin', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'Date de Fin',
                    ],
            ))
            ->add('idVehicule', HiddenType::class, array(
                'attr' => [
                    'value' => 0,
                ],
            ))
            ->add('idOffreLocation', HiddenType::class, array(
                'attr' => [
                    'value' => 0,
                ],
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Valider réservation',
            ));
    }

}