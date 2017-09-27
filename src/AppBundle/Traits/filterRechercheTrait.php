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
        $elasticSearchParameters = array();

        if ($date = explode(' - ', $request->get('recherche')['date'])) {
            $elasticSearchParameters['dateDebut'] = $date[0];
            $elasticSearchParameters['dateFin'] = $date[1];
        }

        return $this->getResultFilter($elasticSearchParameters);
    }

    public function getResultFilter($filter)
    {
        $finder = $this->container->get('fos_elastica.finder.app.offreLocation');
        $filter['dateDebut'] = '2017-06';

        $boolQuery = new \Elastica\Query\BoolQuery();

        if (!empty($filter['dateDebut'])){
            $dateDebutQuery = new \Elastica\Query\Terms();
            $dateDebutQuery->setTerms('dateDebut', array($filter['dateDebut']));
            $boolQuery->addMust($dateDebutQuery);
        }


        dump( $finder->find($boolQuery));die;
    }
}