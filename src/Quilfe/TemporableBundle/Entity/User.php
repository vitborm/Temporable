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

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Quilfe\TemporableBundle\Entity\User
 *
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Entity
 */
class User extends BaseUser
{
    const DEFAULT_PERIOD_LENGTH = 16; // days
    const DAYS_RATIO = 0.5;
    const HOURS_PER_DAY = 6.5;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", length=50, nullable=true)
     */
    private $phone;

    /**
     * @var array $timeUnits
     *
     * @ORM\OneToMany(targetEntity="TimeUnit", mappedBy="user")
     */
    protected $timeUnits;

    /**
     * @var array $tasks
     *
     * @ORM\OneToMany(targetEntity="Task", mappedBy="user")
     */
    protected $tasks;

    /**
     * @var \DateTime $workStartAt
     *
     * @ORM\Column(name="work_start_at", type="datetime", nullable=true)
     */
    protected $workStartAt;

    /**
     * @var \DateTime $firstPeriodStartDate
     *
     * @ORM\Column(name="first_period_start_date", type="datetime", nullable=false)
     */
    protected $firstPeriodStartDate;

    public function __construct()
    {
        parent::__construct();
        $this->timeUnits = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tasks = new \Doctrine\Common\Collections\ArrayCollection();

        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        $this->firstPeriodStartDate = $now;
    }

    /**
     * Validation
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addConstraint(new UniqueEntity([
            'fields' => 'email',
            'message' => 'Данный email уже занят',
        ]));
        $metadata->addPropertyConstraint('email', new Assert\NotBlank([
            'message' => 'Укажите email',
        ]));
        $metadata->addPropertyConstraint('email', new Assert\Length([
            'max' => 64,
        ]));
        $metadata->addPropertyConstraint('email', new Assert\Email([
            'message' => 'Неверный формат электронной почты',
        ]));
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Add timeUnit
     *
     * @param TimeUnit $timeUnit
     *
     * @return User
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
     * Get current period start
     *
     * @param  integer   $days (default: null)
     * @return \DateTime
     */
    public function getCurrentPeriodStart($days = null)
    {
        $start = new \DateTime();
        if (null === $days) {
            $days = round(self::DEFAULT_PERIOD_LENGTH / 2);
        }

        if (false) { // if perioding
            $diff = $start->getTimestamp() - $this->firstPeriodStartDate->getTimestamp();
            $days = $diff / (60 * 60 * 24);

            $dayIndex = ((int) $days) % self::DEFAULT_PERIOD_LENGTH;
            $start->sub(new \DateInterval('P' . $dayIndex . 'D'));
        } else {
            $start->sub(new \DateInterval('P' . $days . 'D'));
        }

        $start->setTime(0, 0, 0);

        return $start;
    }

    public static function getDefaultTotalTime()
    {
        return (int) 60 * self::DAYS_RATIO * self::DEFAULT_PERIOD_LENGTH * self::HOURS_PER_DAY;
    }

    /**
     * Set workStartAt
     *
     * @param \DateTime $workStartAt
     *
     * @return User
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
     * Set firstPeriodStartDate
     *
     * @param \DateTime $firstPeriodStartDate
     *
     * @return User
     */
    public function setFirstPeriodStartDate($firstPeriodStartDate)
    {
        $this->firstPeriodStartDate = $firstPeriodStartDate;

        return $this;
    }

    /**
     * Get firstPeriodStartDate
     *
     * @return \DateTime
     */
    public function getFirstPeriodStartDate()
    {
        return $this->firstPeriodStartDate;
    }

    /**
     * Add task
     *
     * @param Task $task
     *
     * @return User
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
}
