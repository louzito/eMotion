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
        
        if ($request->get('recherche')['dateDebut']) {
            $date = \DateTime::createFromFormat('d/m/Y', $request->get('recherche')['dateDebut']);
            $date = $date->format(DATE_ATOM);
            $filter['dateDebut'] = $date;

            $date = \DateTime::createFromFormat('d/m/Y', $request->get('recherche')['dateFin']);
            $date = $date->format(DATE_ATOM);
            $filter['dateFin'] = $date;
        }

        if ($request->get('recherche')['ville']) {
            $filter['ville'] = $request->get('recherche')['ville'];
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

        if (!empty($filter['ville'])) {
            $boolQuery->addMust(new Query\QueryString($filter['ville']));
        }

        return $finder->find($boolQuery);
    }
}