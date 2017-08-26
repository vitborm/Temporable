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
namespace Quilfe\TemporableBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ReportController extends Controller
{
    /**
     * @Template()
     */
    public function projectsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $type = $request->get('type');
        if ('' === $type || null === $type) {
            $type = $this->get('session')->get('project_report_type');
        }
        if ('' === $type || null === $type) {
            $type = 'exp';
        }
        $this->get('session')->set('project_report_type', $type);

        if ($type == 'exp') {
            $currentPeriodStart = null;
            $currentPeriodData = $em->getRepository('QuilfeTemporableBundle:Project')
                ->getExponentialTimeDistribution();
            $planned = $em->getRepository('QuilfeTemporableBundle:TimeUnit')
                ->getExpectedExponentialTime();
        } else {
            $currentPeriodStart = $this->getUser()->getCurrentPeriodStart((int) $type);
            $currentPeriodData = $em->getRepository('QuilfeTemporableBundle:Project')
                ->getTimeDistribution($currentPeriodStart);
            $planned = $em->getRepository('QuilfeTemporableBundle:TimeUnit')
                ->getExpectedTime($currentPeriodStart);
        }

        $sum = 0;
        foreach ($currentPeriodData as &$projectData) {
            $sum += $projectData['time'];
            $projectData['tasks'] = $em->getRepository('QuilfeTemporableBundle:Task')
                ->getTaskData($projectData['id']);
        }

        if (null === $planned) {
            $planned = $sum;
        }

        $reports = [
            [
                'type' => 'exp',
                'name' => 'За всё время со старением',
            ],
            [
                'type' => 'default',
                'name' => 'За полпериода',
            ],
            [
                'type' => 0,
                'name' => 'За сегодня',
            ],
            [
                'type' => 1,
                'name' => 'За вчера и сегодня',
            ],
            [
                'type' => 3,
                'name' => 'За 3 дня',
            ],
            [
                'type' => 5,
                'name' => 'За 5 дней',
            ],
            [
                'type' => 7,
                'name' => 'За неделю',
            ],
            [
                'type' => 14,
                'name' => 'За 2 недели',
            ],
            [
                'type' => 31,
                'name' => 'За месяц',
            ],
        ];

        return [
            'currentPeriodData' => $currentPeriodData,
            'sum' => $sum,
            'planned' => $planned,
            'currentPeriodStart' => $currentPeriodStart,
            'reports' => $reports,
            'currentType' => $type,
            'todayTime' => $em->getRepository('QuilfeTemporableBundle:TimeUnit')->getTodayTime(),
        ];
    }

    /**
     * @Template()
     */
    public function totalTableAction()
    {
        $em = $this->getDoctrine()->getManager();
        $distribution = $em->getRepository('QuilfeTemporableBundle:Project')->getTotalDistribution();

        return [
            'distribution' => $distribution,
        ];
    }

    /**
     * @Template()
     */
    public function daysAction()
    {
        $em = $this->getDoctrine()->getManager();
        $currentPeriodStart = $this->getUser()->getCurrentPeriodStart();
        $days = $em->getRepository('QuilfeTemporableBundle:TimeUnit')
            ->getDays($currentPeriodStart);

        $sum = 0;
        $cnt = 0;
        foreach ($days as $day) {
            if (!$day['isToday']) {
                $sum += $day['time'];
                $cnt++;
            }
        }

        return [
            'days' => $days,
            'currentPeriodStart' => $currentPeriodStart,
            'sum' => $sum,
            'avg' => $cnt > 0 ? ($sum / $cnt) : 0,
        ];
    }
}
