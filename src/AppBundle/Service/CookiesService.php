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

        if ($params['dateDebut']) {
            $rep->headers->setCookie(new Cookie('dateDebut', $params['dateDebut']));
        }

        if ($params['dateFin']) {
            $rep->headers->setCookie(new Cookie('dateFin', $params['dateFin']));
        }

        if ($params['ville']) {
            $rep->headers->setCookie(new Cookie('ville', $params['ville']));
        }

        if ($params['typeVehicule']) {
            $rep->headers->setCookie(new Cookie('typeVehicule', $params['typeVehicule']));
        }

        if ($params['prixMinJ'] && $params['prixMinJ'] > 0) {
            $rep->headers->setCookie(new Cookie('prixMinJ', $params['prixMinJ']));
        }

        if ($params['prixMaxJ'] && $params['prixMaxJ'] > 0) {
            $rep->headers->setCookie(new Cookie('prixMaxJ', $params['prixMaxJ']));
        }

        if ($params['idVehicule']) {
            $rep->headers->setCookie(new Cookie('idVehicule', $params['idVehicule']));
        }

        return $rep;
    }

    public function setDataForm($request, $form)
    {
        $dateDebut = \DateTime::createFromFormat('d/m/Y', $request->cookies->get('dateDebut'));
        $dateFin = \DateTime::createFromFormat('d/m/Y', $request->cookies->get('dateFin'));

        if ($now = new \DateTime('NOW') < $dateFin) {
            $form->get('dateDebut')->setData($dateDebut);
            $form->get('dateFin')->setData($dateFin);
        }

        $form->get('ville')->setData($request->cookies->get('ville'));
        $form->get('typeVehicule')->setData($request->cookies->get('typeVehicule'));

        return $form;
    }
}