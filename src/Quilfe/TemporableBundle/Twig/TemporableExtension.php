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
namespace Quilfe\TemporableBundle\Twig;

use Quilfe\TemporableBundle\Entity\Project;

class TemporableExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            'time' => new \Twig_Filter_Method($this, 'timeFilter'),
            'hasRedTasks' => new \Twig_Filter_Method($this, 'hasRedTasksFilter'),
        ];
    }

    public function timeFilter($time, $onlyHours = false)
    {
        $time = (int) ceil($time);
        if (!$time) {
            $time = 1;
        }
        $string = '';
        if (($hours = (int) ($time / 60)) > 0) {
            $string = $hours . ' ч.';
        }
        if (!$onlyHours && $time % 60 > 0) {
            $string .= ' ' . ($time % 60) . ' мин.';
        }

        return $string;
    }

    public function hasRedTasksFilter(Project $project)
    {
        foreach ($project->getTasks() as $task) {
            if (!$task->getDone() && $task->getRed()) {
                return true;
            }
        }

        return false;
    }

    public function getName()
    {
        return 'quilfe.temporable.twig_extension';
    }
}
