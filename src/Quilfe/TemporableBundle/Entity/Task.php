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
 * Quilfe\TemporableBundle\Entity\Task
 *
 * @ORM\Table(
 *     name="task",
 *     indexes={@ORM\Index(name="task_project_id_idx", columns={"project_id"})})
 * @ORM\Entity(repositoryClass="Quilfe\TemporableBundle\Entity\Repository\TaskRepository")
 */
class Task
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
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="tasks")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $project;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tasks")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $user;

    /**
     * Is done
     *
     * @var boolean $done
     *
     * @ORM\Column(name="done", type="boolean", nullable=false, options={"default"=0})
     */
    protected $done = false;

    /**
     * Is important
     *
     * @var boolean $red
     *
     * @ORM\Column(name="red", type="boolean", nullable=false, options={"default"=0})
     */
    protected $red = false;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt =  new \DateTime();
        $this->done = false;
        $this->red = false;
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
     * @return Task
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
     * @return Task
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
     * @return Task
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
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param  \DateTime $createdAt
     * @return Task
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set done
     *
     * @param boolean $done
     *
     * @return Task
     */
    public function setDone($done)
    {
        $this->done = $done;

        return $this;
    }

    /**
     * Get done
     *
     * @return boolean
     */
    public function getDone()
    {
        return $this->done;
    }

    /**
     * Set red
     *
     * @param boolean $red
     *
     * @return Task
     */
    public function setRed($red)
    {
        $this->red = $red;

        return $this;
    }

    /**
     * Get red
     *
     * @return boolean
     */
    public function getRed()
    {
        return $this->red;
    }
}
