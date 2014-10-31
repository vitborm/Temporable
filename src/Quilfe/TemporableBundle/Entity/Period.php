<?php

namespace Quilfe\TemporableBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 *
 * Quilfe\TemporableBundle\Entity\Period
 *
 * @ORM\Table(name="period")
 * @ORM\Entity(repositoryClass="Quilfe\TemporableBundle\Entity\Repository\PeriodRepository")
 */
class Period
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
     * @var \DateTime $workStartAt
     *
     * @ORM\Column(name="work_start_at", type="datetime", nullable=true)
     */
    protected $workStartAt;

    /**
     * @var \DateTime $periodStart
     *
     * @ORM\Column(name="period_start", type="datetime", nullable=true)
     */
    protected $periodStart;

    /**
     * @var \DateTime $periodEnding
     *
     * @ORM\Column(name="period_ending", type="datetime", nullable=true)
     */
    protected $periodEnding;

    /**
     * @var array $timeUnits
     *
     * @ORM\OneToMany(targetEntity="TimeUnit", mappedBy="period")
     */
    protected $timeUnits;

    public function __construct($auto = true)
    {
        if ($auto) {
            $now = new \DateTime();
            $now->setTime(0, 0, 0);
            $ending = clone $now;
            $ending->add(new \DateInterval('P11D'));
            $now->setTime(23, 59, 59);

            $this->periodStart = $now;
            $this->periodEnding = $ending;
        }

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
     * Set workStartAt
     *
     * @param \DateTime $workStartAt
     *
     * @return Period
     */
    public function setWorkStartAt($workStartAt)
    {
        $this->workStartAt = $workStartAt;

        return $this;
    }

    /**
     * Get workStartAt
     *
     * @return \DateTime
     */
    public function getWorkStartAt()
    {
        return $this->workStartAt;
    }

    /**
     * Set periodStart
     *
     * @param \DateTime $periodStart
     *
     * @return Period
     */
    public function setPeriodStart($periodStart)
    {
        $this->periodStart = $periodStart;

        return $this;
    }

    /**
     * Get periodStart
     *
     * @return \DateTime
     */
    public function getPeriodStart()
    {
        return $this->periodStart;
    }

    /**
     * Set periodEnding
     *
     * @param \DateTime $periodEnding
     *
     * @return Period
     */
    public function setPeriodEnding($periodEnding)
    {
        $this->periodEnding = $periodEnding;

        return $this;
    }

    /**
     * Get periodEnding
     *
     * @return \DateTime
     */
    public function getPeriodEnding()
    {
        return $this->periodEnding;
    }

    /**
     * Add timeUnit
     *
     * @param TimeUnit $timeUnit
     *
     * @return Period
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
