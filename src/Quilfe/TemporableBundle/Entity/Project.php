<?php

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
     * Constructor
     */
    public function __construct()
    {
        $this->timeUnits = new \Doctrine\Common\Collections\ArrayCollection();
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
}
