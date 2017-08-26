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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Quilfe\TemporableBundle\Entity\TimeUnit;
use Quilfe\TemporableBundle\Entity\Task;

class MainController extends Controller
{
    /**
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $makeFlush = false;

        $routines = $em->getRepository('QuilfeTemporableBundle:Routine')->findAll();
        foreach ($routines as $routine) {
            $toBeExecutedAt = clone $routine->getLastExecuted();
            $toBeExecutedAt->setTime(0, 0, 0);
            $period = new \DateInterval('P' . $routine->getPeriod() . 'D');
            $toBeExecutedAt->add($period);
            $toBeExecutedAt->modify('+4 hour'); // день начинается с 4 утра, до этого момента пока предыдущий :-)

            if ($toBeExecutedAt < new \DateTime()) {
                $task = new Task();

                $task->setName($routine->getName())
                    ->setProject($routine->getProject())
                    ->setUser($this->getUser())
                    ->setRed(true)
                    ->setCreatedAt($toBeExecutedAt);
                ;

                $routine->setLastExecuted($toBeExecutedAt);
                $em->persist($routine);
                $em->persist($task);
                $makeFlush = true;
            }
        }

        if ($makeFlush) {
            $em->flush();
        }

        $projects = $em->getRepository('QuilfeTemporableBundle:Project')->findProjects();

        return [
            'workStartAt' => $this->getUser()->getWorkStartAt(),
            'projects' => $projects,
        ];
    }

    public function startWorkAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $user->setWorkStartAt(new \DateTime());
        $em->merge($user);
        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }

    public function stopWorkAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $user->setWorkStartAt(null);
        $em->merge($user);
        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }

    protected function getTimeUnit($time = null)
    {
        $timeUnit = new TimeUnit();
        $timeUnit->setUser($this->getUser());
        if ($time) {
            $timeUnit->setTime((int) $time);
        }

        return $timeUnit;
    }

    public function addTimeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projectId = (int) $request->get('projectId');
        $time = (int) $request->get('time');
        if (!$time || !$projectId) {
            return new JsonResponse(['status' => 'Время или проект не указаны или указаны некорректно']);
        }

        $project = $em->getRepository('QuilfeTemporableBundle:Project')
            ->findOneById($projectId);
        if (!$project) {
            return new JsonResponse(['status' => 'Проект не найден']);
        }

        $timeUnit = $this->getTimeUnit($time);
        $timeUnit->setProject($project);
        $em->persist($timeUnit);
        $em->flush();

        return new JsonResponse(['status' => 'ok']);
    }

    public function markTimeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $projectId = (int) $request->get('projectId');
        if (!$projectId) {
            return new JsonResponse(['status' => 'Проект не указан или указан некорректно']);
        }

        $project = $em->getRepository('QuilfeTemporableBundle:Project')
            ->findOneById($projectId);
        if (!$project) {
            return new JsonResponse(['status' => 'Проект не найден']);
        }

        $user = $this->getUser();
        $workStartAt = $user->getWorkStartAt();
        $now = new \DateTime();
        if (!$workStartAt || $now < $workStartAt) {
            return new JsonResponse(['status' => 'Работа в периоде сейчас не отслеживается']);
        }

        $difference = (int) round(($now->getTimestamp() - $workStartAt->getTimestamp()) / 60);
        if (!$difference || $difference <= 1) {
            return new JsonResponse(['status' => 'Слишком мелкий кусок времени']);
        }

        $user->setWorkStartAt($now);
        $timeUnit = $this->getTimeUnit($difference);
        $timeUnit->setProject($project);
        $em->merge($user);
        $em->persist($timeUnit);
        $em->flush();

        return new JsonResponse([
            'status' => 'ok',
            'time' => $this->get('quilfe.temporable.twig_extension')
                ->timeFilter($difference),
        ]);
    }
}
