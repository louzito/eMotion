<?php
/**
 * Created by PhpStorm.
 * User: axel
 * Date: 27/09/17
 * Time: 11:00
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateDebut', DateType::class, array(
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'Date de Début',
                    ],
                'format' => 'dd/MM/yyyy',
            ))
            ->add('dateFin', DateType::class, array(
                'widget' => 'single_text',
                // do not render as type="date", to avoid HTML5 date pickers
                'html5' => false,
                // add a class that can be selected in JavaScript
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'Date de Fin',
                    ],
                'format' => 'dd/MM/yyyy',
            ))
            ->add('ville', ChoiceType::class, array(
                'choices'  => array(
                    'Paris' => 'Paris',
                    'Lyon' => 'Lyon',
                ),
            ))
            ->add('typeVehicule', ChoiceType::class, array(
                'choices'  => array(
                    'Voiture' => 'voiture',
                    'Scooter' => 'scooter',
                ),
            ))
            ->add('prixMinJ', HiddenType::class, array(
                'attr' => [
                    'value' => -1,
                ],
           ))
            ->add('prixMaxJ', HiddenType::class, array(
                'attr' => [
                    'value' => -1,
                ],
            ))
            ->add('submit', SubmitType::class, array(
                'label' => 'Rechercher',
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

}