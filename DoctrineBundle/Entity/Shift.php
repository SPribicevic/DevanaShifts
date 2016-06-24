<?php

namespace Devana\DoctrineBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shift
 *
 * @ORM\Table()
 * @ORM\Entity
 *
 */
class Shift
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startsAt", type="time")
     */
    private $startsAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endsAt", type="time")
     */
    private $endsAt;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Employee",inversedBy="shifts")
     */
    private $employees;



    function __construct()
    {
        $this->employees = new ArrayCollection();
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
     * Set date
     *
     * @param \DateTime $date
     * @return Shift
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
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
     * Set startsAt
     *
     * @param \DateTime $startsAt
     * @return Shift
     */
    public function setStartsAt($startsAt)
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    /**
     * Get startsAt
     *
     * @return \DateTime 
     */
    public function getStartsAt()
    {
        return $this->startsAt;
    }

    /**
     * Set endsAt
     *
     * @param \DateTime $endsAt
     * @return Shift
     */
    public function setEndsAt($endsAt)
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    /**
     * Get endsAt
     *
     * @return \DateTime 
     */
    public function getEndsAt()
    {
        return $this->endsAt;
    }

    /**
     * @param mixed $workers
     */
    public function setEmployees($workers)
    {
        $this->employees = $workers;
    }

    /**
     * @return ArrayCollection
     */
    public function getEmployees()
    {
        return $this->employees;
    }

    /**
     * @return Shift
     */
    public function getShift()
    {
        return $this;
    }

    /**
     * @param mixed $demands
     */
    public function setDemands($demands)
    {
        $this->demands = $demands;
    }

    /**
     * @return mixed
     */
    public function getDemands()
    {
        return $this->demands;
    }

}
