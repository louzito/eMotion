<?php
/**
 * Created by PhpStorm.
 * User: axel
 * Date: 27/09/17
 * Time: 11:55
 */

namespace AppBundle\Traits;

use Elastica\Query;

trait filterRechercheTrait
{
    public function getFilter($request)
    {
        $filter = array();       
        $filter = $request->get('recherche');

        if ($request->get('recherche')['dateDebut']) {
            $date = \DateTime::createFromFormat('d/m/Y', $request->get('recherche')['dateDebut']);
            $date = $date->format(DATE_ATOM);
            $filter['dateDebut'] = $date;

            $date = \DateTime::createFromFormat('d/m/Y', $request->get('recherche')['dateFin']);
            $date = $date->format(DATE_ATOM);
            $filter['dateFin'] = $date;
        }

        $offres = $this->getResultFilter($filter);
        $offresDispo = [];
        foreach($offres as $offre)
        {
           if($this->isReservationDisponible($filter, $offre->getVehicule())){
                $offresDispo[] = $offre;
           }
        }

        return $offresDispo;
    }

    public function getFilterAdmin($request)
    {
        $filter = array();
        $filter = $request->request->get('recherche_admin');

        if ($filter['dateDebut'] && $filter['dateFin']) {
            $date = \DateTime::createFromFormat('d/m/Y', $filter['dateDebut']);
            $date = $date->format(DATE_ATOM);
            $filter['dateDebut'] = $date;

            $date = \DateTime::createFromFormat('d/m/Y', $filter['dateFin']);
            $date = $date->format(DATE_ATOM);
            $filter['dateFin'] = $date;
        }

        $offres = $this->getResultFilter($filter);
        $offresDispo = [];

        foreach($offres as $offre)
        {
           if($this->isReservationDisponible($filter, $offre->getVehicule())){
                $offresDispo[] = $offre;
           }
        }
        return $offresDispo;
    }


    public function getResultFilter($filter)
    {
        $finder = $this->container->get('fos_elastica.finder.app.offreLocation');

        $boolQuery = new \Elastica\Query\BoolQuery();

        if (!empty($filter['dateDebut'])){
            $boolQuery->addMust(new Query\Range('dateDebut', array('lte' => $filter['dateDebut'])));
        }

        if (!empty($filter['dateFin'])){
            $boolQuery->addMust(new Query\Range('dateFin', array('gte' => $filter['dateFin'])));
        }

        if (isset($filter['ville']) && !empty($filter['ville'])) {
            $boolQuery->addMust(new Query\QueryString($filter['ville']));
        }

        if (isset($filter['typeVehicule']) && !empty($filter['typeVehicule'])) {
            $boolQuery->addMust(new \Elastica\Query\Match('vehicule.type', $filter['typeVehicule']));
        }
        
        if(isset($filter['prixMinJ']) && isset($filter['prixMaxJ']) && $filter['prixMinJ'] != "-1" && $filter['prixMaxJ'] != "-1"){
            $boolQuery->addMust(new Query\Range('prixJournalier', array(
                'gte' => $filter['prixMinJ'],
                'lte' => $filter['prixMaxJ'],
                )));
        }

        if(isset($filter['idVehicule']) && $filter['idVehicule'] != "0"){
            $boolQuery->addMust(new \Elastica\Query\Match('vehicule.id', $filter['idVehicule']));
        }

        return $finder->find($boolQuery);
    }

    public function isReservationDisponible($filter, $vehicule)
    {
        $finder = $this->container->get('fos_elastica.finder.app.reservation');

        $boolQuery = new \Elastica\Query\BoolQuery();
        if (!empty($filter['dateDebut']) && !empty($filter['dateFin']) && !empty($vehicule)){
            $boolQuery->addMust(new \Elastica\Query\Match('vehicule.id', $vehicule->getId()));
            $boolQuery->addShould(new Query\Range('dateDebut', array(
                'gte' => $filter['dateDebut'],
                'lte' => $filter['dateFin']
                ))); // CAS B ET D
            $boolQuery->addShould(new Query\Range('dateFin', array(
                'gte' => $filter['dateDebut'],
                'lte' => $filter['dateFin']
                ))); // CAS C ET D
            $boolQuery->addShould(new Query\Range('dateDebut', array(
                'lte' => $filter['dateDebut']
                ))); // CAS E ET C
            $boolQuery->addShould(new Query\Range('dateFin', array(
                'gte' => $filter['dateFin']
                ))); // CAS E ET B
            $boolQuery->setMinimumNumberShouldMatch(2);
        }
        return empty($finder->find($boolQuery));
    }
}