<?php
/**
 *  Copyright 2014 Vitaly Bormotov <bormvit@mail.ru>
 *
 *  This file is part of Quilfe Temporable.
 *
 *  Quilfe Temporable is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Quilfe Temporable is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Quilfe Temporable. If not, see <http://www.gnu.org/licenses/>.
**/
namespace Quilfe\TemporableBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

class ProjectRepository extends EntityRepository
{
    public function getTimeDistribution(\DateTime $currentPeriodStart)
    {
        $qb = $this->createQueryBuilder('pr');
        $q = $qb->select(
            'pr.id as id,
            pr.name as project,
            pr.actual as actual,
            pr.minPercent as minPercent,
            pr.maxPercent as maxPercent,
            SUM(t.time) as time'
        )
            ->leftJoin('pr.timeUnits', 't', 'WITH', 't.date >= :start')
            ->groupBy('pr.name')
            ->addOrderBy('pr.actual', 'DESC')
            ->addOrderBy('time', 'DESC')
            ->addOrderBy('pr.id', 'ASC')
            ->setParameter('start', $currentPeriodStart)
        ;

        return $q->getQuery()->getArrayResult();
    }

    public function getExponentialTimeDistribution()
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('project', 'project');
        $rsm->addScalarResult('actual', 'actual');
        $rsm->addScalarResult('min_percent', 'minPercent');
        $rsm->addScalarResult('max_percent', 'maxPercent');
        $rsm->addScalarResult('time', 'time');

        $q = $this->getEntityManager()->createNativeQuery(
            "SELECT
                pr.id as id,
                pr.name as project,
                pr.actual as actual,
                pr.min_percent as min_percent,
                pr.max_percent as max_percent,
                SUM(t.time * exp(datediff(t.date, now()) / 13)) as time
            FROM
                project pr
            LEFT JOIN
                time_unit t ON t.project_id = pr.id
            WHERE
                t.date > :bottomline
            GROUP BY
                pr.id, pr.name, pr.actual
            ORDER BY
                pr.actual DESC,
                time DESC,
                pr.id ASC
            ",
            $rsm
        )->setParameter('bottomline', new \DateTime('-3 month'));

        return $q->getResult();
    }

    public function findProjects()
    {
        return $this->createQueryBuilder('pr')
            ->select('pr')
            //->leftJoin('pr.timeUnits', 'tu')
            //->groupBy('pr.id')
            ->addOrderBy('pr.actual', 'DESC')
            //->addOrderBy('cnt', 'DESC')
            ->addOrderBy('pr.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
