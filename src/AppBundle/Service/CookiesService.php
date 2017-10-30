<?php
/**
 * Created by PhpStorm.
 * User: axel
 * Date: 02/10/17
 * Time: 14:48
 */

namespace AppBundle\Service;

use AppBundle\Form\RechercheType;
use Symfony\Component\DependencyInjection\Tests\Compiler\C;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class CookiesService
{
    public function setCookiesRecherche($params)
    {
        $rep = new Response();

        if ($params) {
            $rep->headers->setCookie(new Cookie('recherche', serialize($params), time() + 3600*24*30));
        }

        return $rep;
    }

    public function setDataForm($request, $form, $params = null)
    {
        if(!$params){
            $params = unserialize($request->cookies->get('recherche'));
        }

        $dateDebut = \DateTime::createFromFormat('d/m/Y', $params['dateDebut']);
        $dateFin = \DateTime::createFromFormat('d/m/Y', $params['dateFin']);
        if ($now = new \DateTime('NOW') < $dateFin) {
            $form->get('dateDebut')->setData($dateDebut);
            $form->get('dateFin')->setData($dateFin);
        }
        $form->get('ville')->setData($params['ville']);
        $form->get('typeVehicule')->setData($params['typeVehicule']);
        
        return $form;
    }
}