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
 * Quilfe\TemporableBundle\Entity\Routine
 *
 * @ORM\Table(
 *     name="routine",
 *     indexes={@ORM\Index(name="routine_project_id_idx", columns={"project_id"})})
 * @ORM\Entity
 */
class Routine
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
     * @ORM\Column(name="name", type="string", length=1000, nullable=false)
     */
    protected $name;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="routines")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $project;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="routines")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;

    /**
     * @var \DateTime $lastExecuted
     *
     * @ORM\Column(name="last_executed", type="date", nullable=false)
     */
    protected $lastExecuted;

    /**
     * Period in days to be executed
     * @var integer $period
     *
     * @ORM\Column(name="period", type="integer", nullable=false)
     */
    protected $period;

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
     * @return Routine
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
     * Set project
     *
     * @param Project $project
     *
     * @return Routine
     */
    public function setProject(Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Routine
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get lastExecuted
     *
     * @return \DateTime
     */
    public function getLastExecuted()
    {
        return $this->lastExecuted;
    }

    /**
     * Set lastExecuted
     *
	 * @param  \DateTime $lastExecuted
     * @return Routine
     */
    public function setLastExecuted(\DateTime $lastExecuted)
    {
        $this->lastExecuted = clone $lastExecuted;
        $this->lastExecuted->setTime(0, 0, 0);

        return $this;
    }

    /**
     * Set period
     *
     * @param int $period
     *
     * @return Routine
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return int
     */
    public function getPeriod()
    {
        return $this->period;
    }
}
