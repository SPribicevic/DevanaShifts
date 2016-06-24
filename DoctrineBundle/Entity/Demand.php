<?php
/**
 * Created by PhpStorm.
 * User: Paja
 * Date: 29.8.14.
 * Time: 11.39
 */

namespace Devana\DoctrineBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;


/**
 * Class Demand
 *
 * @Entity()
 * @Table(name="demand")
 *
 */
class Demand {

    /**
     * @var integer
     *
     * @Id()
     * @Column(name="id",type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Employee
     *
     * @ManyToOne(targetEntity="Employee")
     */
    private $employee;

    /**
     * @var Employee
     *
     * @ManyToOne(targetEntity="Employee")
     */
    private $targetEmployee;

    /**
     * @var \DateTime
     *
     * @Column(name="time",type="datetime")
     */
    private $time;

    /**
     *  @Column(name="state",type="string",length=255)
     */
    private $state;

    /**
     * @var Shift
     *
     *  @ManyToOne(targetEntity="Shift")
     */
    private $shift;

    /**
     * @var Shift
     *
     * @ManyToOne(targetEntity="Shift")
     */
    private $requestedShift;

    /**
     * @var String
     * @Column(name="comment",length=255)
     */
    private $comment;

    /**
     * @var /DateTime
     * @Column(name="expiresAt",type="datetime")
     */
    private $expiresAt;

    /**
     * @var /DateTime
     * @Column(name="respondedAt",type="datetime",nullable=true)
     */
    private $respondedAt;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $targetEmployee
     */
    public function setTargetEmployee($targetEmployee)
    {
        $this->targetEmployee = $targetEmployee;
    }

    /**
     * @return int
     */
    public function getTargetEmployee()
    {
        return $this->targetEmployee;
    }

    /**
     * @param mixed $shift
     */
    public function setShift($shift)
    {
        $this->shift = $shift;
    }

    /**
     * @return mixed
     */
    public function getShift()
    {
        return $this->shift;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $employee
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;
    }

    /**
     * @return int
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * @param \DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param String $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return String
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param \Devana\DoctrineBundle\Entity\Shift $requestedShift
     */
    public function setRequestedShift($requestedShift)
    {
        $this->requestedShift = $requestedShift;
    }

    /**
     * @return \Devana\DoctrineBundle\Entity\Shift
     */
    public function getRequestedShift()
    {
        return $this->requestedShift;
    }

    /**
     * @param mixed $expiresAt
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return mixed
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * @param mixed $respondedAt
     */
    public function setRespondedAt($respondedAt)
    {
        $this->respondedAt = $respondedAt;
    }

    /**
     * @return mixed
     */
    public function getRespondedAt()
    {
        return $this->respondedAt;
    }



}