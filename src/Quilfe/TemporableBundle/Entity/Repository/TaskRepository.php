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

use Quilfe\TemporableBundle\Entity\Project;

class TaskRepository extends EntityRepository
{
    public function getForProject(Project $project, $completed = false, $offset = 0, $limit = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.project = :project')
            ->andWhere('t.done = :completed');

        if (!$completed) {
            $qb->addOrderBy('t.red', 'DESC');
        }

        $qb->addOrderBy('t.createdAt', 'DESC')
            ->setParameter('project', $project)
            ->setParameter('completed', $completed)
            ->setFirstResult($offset)
        ;

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }

    public function getTaskData($projectId)
    {
        $q = $this->getEntityManager()->createQuery('
            SELECT
                count(t.id) as opened,
                count(t_red.id) as red
            FROM
                QuilfeTemporableBundle:Task t
            LEFT JOIN
                QuilfeTemporableBundle:Task t_red WITH (t_red.id = t.id AND t_red.red = true)
            WHERE
                t.project = :project
                AND t.done = false
        ')->setParameter('project', $projectId);

        return $q->getResult()[0];
    }

    public function clearOld($period = '3M')
    {
        $this->getEntityManager()
            ->createQuery('DELETE FROM QuilfeTemporableBundle:Task t WHERE t.createdAt < :boundary AND t.done = true')
            ->setParameter('boundary', (new \DateTime())->sub(new \DateInterval('P' . $period)))
            ->execute()
        ;
    }
}
