<?php
/**
 * Created by PhpStorm.
 * User: axel
 * Date: 05/10/17
 * Time: 14:31
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class ReservationParVoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('dateDebut', DateType::class, array(
            'widget' => 'single_text',
            'html5' => false,
            'attr' => [
                'class' => 'js-datepicker',
                'style' => 'display: none',
            ],
            'label' => false
            ))
            ->add('dateFin', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'js-datepicker',
                    'style' => 'display: none',
                ],
                'label' => false
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
                'attr' => array(
                    'class' => 'submit-liste-vehicule',
                )
            ));
    }

}