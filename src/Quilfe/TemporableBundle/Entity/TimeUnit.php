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
 * Quilfe\TemporableBundle\Entity\TimeUnit
 *
 * @ORM\Table(
 *     name="time_unit",
 *     indexes={@ORM\Index(name="time_unit_date_idx", columns={"date"})})
 * @ORM\Entity(repositoryClass="Quilfe\TemporableBundle\Entity\Repository\TimeUnitRepository")
 */
class TimeUnit
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
     * @var string $note
     *
     * @ORM\Column(name="note", type="string", length=255, nullable=true)
     */
    protected $note;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="timeUnits")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $project;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="timeUnits")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;

    /**
     * Time in minutes
     *
     * @var integer $time
     *
     * @ORM\Column(name="time", type="integer", nullable=false)
     */
    protected $time;

    /**
     * @var \DateTime $date
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    protected $date;

    public function __construct()
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $this->date = $now; // only date
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
     * Set note
     *
     * @param string $note
     *
     * @return TimeUnit
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set project
     *
     * @param Project $project
     *
     * @return TimeUnit
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
     * Set time
     *
     * @param integer $time
     *
     * @return TimeUnit
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return TimeUnit
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
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
	 * @param  \DateTime $date
     * @return TimeUnit
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }
}
