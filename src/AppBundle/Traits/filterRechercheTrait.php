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

        if ($tab = explode(' - ', $request->get('recherche')['date'])) {
            $date = new \DateTime($tab[0]);
            $date = $date->format(DATE_ATOM);
            $filter['dateDebut'] = $date;

            $date = new \DateTime($tab[1]);
            $date = $date->format(DATE_ATOM);
            $filter['dateFin'] = $date;
        }

        return $this->getResultFilter($filter);
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

        return $finder->find($boolQuery);
    }
}