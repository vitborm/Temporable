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
use Quilfe\TemporableBundle\Entity\TimeUnit;
use Quilfe\TemporableBundle\Entity\User;

class TimeUnitRepository extends EntityRepository
{
    const TABLE = 'time_unit';

    public function getExpectedExponentialTime()
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total_time', 'totalTime');

        $q = $this->getEntityManager()->createNativeQuery(
            "
            SELECT
                SUM(tug.time) AS total_time
            FROM (
                SELECT
                    tu.date AS date,
                    CASE WHEN
                        tu.date >= :now
                    THEN
                        SUM(tu.time)
                    ELSE
                        IF(SUM(tu.time) > 15, 60 * (" . User::HOURS_PER_DAY . "), SUM(tu.time))
                            * exp(datediff(tu.date, now()) / 13)
                    END AS time
                FROM
                    " . self::TABLE . " tu
                GROUP BY
                    tu.date
                HAVING
                    tu.date > :bottomline
            ) tug
            ",
            $rsm
        )->setParameter('now', $now)
        ->setParameter('bottomline', new \DateTime('-3 month'));

        return $q->getSingleResult()['totalTime'];
    }

    public function getExpectedTime(\DateTime $currentPeriodStart)
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('total_time', 'totalTime');

        $q = $this->getEntityManager()->createNativeQuery(
            "
            SELECT
                (60 * " . User::HOURS_PER_DAY . " * COUNT(
                    CASE WHEN
                        tug.date < :now
                    THEN
                        1
                    ELSE
                        NULL
                    END
                ) + SUM(tug.time)) AS total_time
            FROM (
                SELECT
                    tu.date AS date,
                    CASE WHEN
                        tu.date >= :now
                    THEN
                        SUM(tu.time)
                    ELSE
                        0
                    END AS time
                FROM
                    " . self::TABLE . " tu
                WHERE
                    tu.date >= :start
                GROUP BY
                    tu.date
                HAVING
                    SUM(tu.time) > 15 OR tu.date >= :now
            ) tug
            ",
            $rsm
        )
            ->setParameter('start', $currentPeriodStart)
            ->setParameter('now', $now)
        ;

        return $q->getSingleResult()['totalTime'];
    }

    public function getDays(\DateTime $currentPeriodStart)
    {
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('date', 'date');
        $rsm->addScalarResult('time', 'time');
        $rsm->addScalarResult('is_today', 'isToday');
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $q = $this->getEntityManager()->createNativeQuery(
            "
                SELECT
                    tu.date AS date,
                    SUM(tu.time) AS time,
                    CASE WHEN
                        tu.date >= :now
                    THEN
                        1
                    ELSE
                        0
                    END AS is_today
                FROM
                    " . self::TABLE . " tu
                WHERE
                    tu.date >= :start
                GROUP BY
                    tu.date
            ",
            $rsm
        )
            ->setParameter('start', $currentPeriodStart)
            ->setParameter('now', $now)
        ;

        return $q->getResult();
    }

    public function getTodayTime()
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $q = $this->createQueryBuilder('tu')
            ->select('SUM(tu.time) AS time')
            ->andWhere('tu.date >= :now')
            ->setParameter('now', $now)
        ;

        return $q->getQuery()->getSingleScalarResult();
    }

    public function clearOld($period = '4M')
    {
        $this->getEntityManager()
            ->createQuery('DELETE FROM QuilfeTemporableBundle:TimeUnit tu WHERE tu.date < :boundary')
            ->setParameter('boundary', (new \DateTime())->sub(new \DateInterval('P' . $period)))
            ->execute()
        ;
    }

    public function merge()
    {
        $em = $this->getEntityManager();

        $projects = $em->getRepository(Project::class)->findAll();
        $users = $em->getRepository(User::class)->findAll();
        $users[] = null;
        foreach ($projects as $project) {
            foreach ($users as $user) {
                $em->getConnection()->beginTransaction();
                try {
                    $dataQueryBuilder = $this->createQueryBuilder('tu')
                        ->select('SUM(tu.time) as time, tu.date AS date')
                        ->andWhere('tu.project = :project')
                        ->setParameter('project', $project)
                        ->groupBy('tu.date')
                    ;

                    if ($user) {
                        $dataQueryBuilder->andWhere('tu.user = :user')
                            ->setParameter('user', $user);
                    } else {
                        $dataQueryBuilder->andWhere('tu.user IS NULL');
                    }

                    $data = $dataQueryBuilder->getQuery()->getResult();

                    $deleteQuery = $em->createQuery(
                        'DELETE FROM QuilfeTemporableBundle:TimeUnit tu WHERE tu.project = :project AND tu.user '
                        . ($user ? '= :user' : 'IS NULL')
                    )->setParameter('project', $project);

                    if ($user) {
                        $deleteQuery->setParameter('user', $user);
                    }

                    $deleteQuery->execute();

                    foreach ($data as $row) {
                        $timeUnit = new TimeUnit();
                        $timeUnit->setProject($project);
                        if ($user) {
                            $timeUnit->setUser($user);
                        }
                        $timeUnit->setTime((int) $row['time']);
                        $timeUnit->setDate($row['date']);

                        $em->persist($timeUnit);
                    }

                    $em->flush();
                    $em->getConnection()->commit();
                } catch (\Exception $e) {
                    $em->getConnection()->rollback();
                    $em->close();

                    throw $e;
                }
            }
        }
    }
}
