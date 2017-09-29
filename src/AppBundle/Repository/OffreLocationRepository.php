<?php

namespace AppBundle\Repository;

/**
 * OffreLocationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OffreLocationRepository extends \Doctrine\ORM\EntityRepository
{
    public function findMinEtMaxPrix()
    {
        $query = $this->createQueryBuilder('ol');
        $query->select('MIN(ol.prixJournalier) AS minP');
        $query->addSelect('MAX(ol.prixJournalier) AS maxP');

        return $query->getQuery()->getSingleResult();
    }
}
