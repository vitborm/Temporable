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
namespace Quilfe\TemporableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * Quilfe\TemporableBundle\Entity\Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Quilfe\TemporableBundle\Entity\Repository\ProjectRepository")
 */
class Project
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    protected $name;

    /**
     * @var array $timeUnits
     *
     * @ORM\OneToMany(targetEntity="TimeUnit", mappedBy="project")
     */
    protected $timeUnits;

    /**
     * @var array $tasks
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="project")
     */
    protected $tasks;

    /**
     * @var boolean $actual
     *
     * @ORM\Column(name="actual", type="boolean", nullable=false, options={"default"=0})
     */
    protected $actual;

    /**
     * @var integer $minPercent
     *
     * @ORM\Column(name="min_percent", type="integer", nullable=true)
     */
    protected $minPercent;

    /**
     * @var integer $maxPercent
     *
     * @ORM\Column(name="max_percent", type="integer", nullable=true)
     */
    protected $maxPercent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->timeUnits = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->actual = false;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add timeUnit
     *
     * @param TimeUnit $timeUnit
     *
     * @return Project
     */
    public function addTimeUnit(TimeUnit $timeUnit)
    {
        $this->timeUnits[] = $timeUnit;

        return $this;
    }

    /**
     * Remove timeUnit
     *
     * @param TimeUnit $timeUnit
     */
    public function removeTimeUnit(TimeUnit $timeUnit)
    {
        $this->timeUnits->removeElement($timeUnit);
    }

    /**
     * Get timeUnits
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimeUnits()
    {
        return $this->timeUnits;
    }

    /**
     * Add task
     *
     * @param Task $task
     *
     * @return Project
     */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;

        return $this;
    }

    /**
     * Remove task
     *
     * @param Task $task
     */
    public function removeTask(Task $task)
    {
        $this->tasks->removeElement($task);
    }

    /**
     * Get tasks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * Set actual
     *
     * @param boolean $actual
     *
     * @return Project
     */
    public function setActual($actual)
    {
        $this->actual = $actual;

        return $this;
    }

    /**
     * Get actual
     *
     * @return boolean
     */
    public function getActual()
    {
        return $this->actual;
    }

    /**
     * Set minPercent
     *
     * @param integer $minPercent
     *
     * @return Project
     */
    public function setMinPercent($minPercent)
    {
        if (!$minPercent && $minPercent !== 0) {
            $this->minPercent = null;
        } else {
            $this->minPercent = max((int) $minPercent, 0);
        }

        return $this;
    }

    /**
     * Get minPercent
     *
     * @return integer
     */
    public function getMinPercent()
    {
        return $this->minPercent;
    }

    /**
     * Set maxPercent
     *
     * @param integer $maxPercent
     *
     * @return Project
     */
    public function setMaxPercent($maxPercent)
    {
        if (!$maxPercent) {
            $this->maxPercent = null;
        } else {
            $this->maxPercent = min((int) $maxPercent, 100);
        }

        return $this;
    }

    /**
     * Get maxPercent
     *
     * @return integer
     */
    public function getMaxPercent()
    {
        return $this->maxPercent;
    }
}
