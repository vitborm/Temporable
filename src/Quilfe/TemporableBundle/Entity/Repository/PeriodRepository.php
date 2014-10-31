<?php

namespace Quilfe\TemporableBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class PeriodRepository extends EntityRepository
{
    public function getCurrentPeriod()
    {
        $q = $this->getEntityManager()
            ->createQueryBuilder('p')
            ->select('p')
            ->from('QuilfeTemporableBundle:Period', 'p')
            ->where('p.periodStart < :now')
            ->andWhere('p.periodEnding > :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('p.periodStart', 'DESC')
            ->setMaxResults(1)
        ;

        return $q->getQuery()->getOneOrNullResult();
    }
}
