<?php

namespace Quilfe\TemporableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * Quilfe\TemporableBundle\Entity\TimeUnit
 *
 * @ORM\Table(name="time_unit")
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
     * @var Period
     *
     * @ORM\ManyToOne(targetEntity="Period", inversedBy="timeUnits")
     * @ORM\JoinColumn(name="period_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $period;

    /**
     * @var integer $time
     *
     * @ORM\Column(name="time", type="integer", nullable=false)
     */
    protected $time;

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
     * Set period
     *
     * @param Period $period
     *
     * @return TimeUnit
     */
    public function setPeriod(Period $period = null)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Get period
     *
     * @return Period
     */
    public function getPeriod()
    {
        return $this->period;
    }
}
