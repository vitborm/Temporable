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

use Quilfe\TemporableBundle\Entity\Task;
use Quilfe\TemporableBundle\Entity\Project;

class TaskController extends Controller
{
    public function projectTasksAction(Project $project)
    {
        $em = $this->getDoctrine()->getManager();

        // сортировка, пагинация
        $tasks = $em->getRepository('QuilfeTemporableBundle:Task')->getForProject($project);
        $completed = $em->getRepository('QuilfeTemporableBundle:Task')->getForProject($project, true, 0, 7);

        return $this->render('QuilfeTemporableBundle:Task:list.html.twig', [
            'tasks' => array_merge($tasks, $completed),
        ]);
    }

    public function actualAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tasks = $em->getRepository(Task::class)->findBy(
            ['red' => true, 'done' => false],
            ['project' => 'ASC', 'createdAt' => 'DESC']
        );

        $projectTasks = [];
        foreach ($tasks as $task) {
            $projectName = $task->getProject()->getName();
            if (!isset($projectTasks[$projectName])) {
                $projectTasks[$projectName] = [];
            }

            $projectTasks[$projectName][] = $task;
        }

        return $this->render('QuilfeTemporableBundle:Task:actual.html.twig', [
            'projectTasks' => $projectTasks,
            'tasksCount' => count($tasks),
        ]);
    }

    public function addAction(Request $request, $project)
    {
        $em = $this->getDoctrine()->getManager();
        $project = $em->getRepository('QuilfeTemporableBundle:Project')->findOneById($project);
        $name = $request->get('name');

        if (!$project || !$name) {
            throw $this->createNotFoundException();
        }

        $task = new Task();
        $task->setName($name)
            ->setProject($project)
            ->setUser($this->getUser())
        ;

        $em->persist($task);
        $em->flush();

        return $this->render('QuilfeTemporableBundle:Task:list.html.twig', [
            'tasks' => [$task],
        ]);
    }

    public function markDoneAction(Request $request, $task)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('QuilfeTemporableBundle:Task')->findOneById($task);

        if ($task->getUser()->getId() != $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $task->setDone(true);

        $em->persist($task);
        $em->flush();

        return $this->render('QuilfeTemporableBundle:Task:list.html.twig', [
            'tasks' => [$task],
        ]);
    }

    public function changeRedAction(Request $request, $task)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('QuilfeTemporableBundle:Task')->findOneById($task);

        if ($task->getUser()->getId() != $this->getUser()->getId()) {
            throw $this->createAccessDeniedException();
        }

        $task->setRed($request->get('make') == 'red');

        $em->persist($task);
        $em->flush();

        return new JsonResponse(['ok']);
    }
}
