<?php
/**
 * Created by PhpStorm.
 * User: Paja
 * Date: 8.9.14.
 * Time: 14.27
 */

namespace Devana\SecurityBundle\Security\Authorization\Voter;


use Devana\DoctrineBundle\Entity\Demand;
use Devana\DoctrineBundle\Entity\Employee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class DemandVoter implements VoterInterface {

    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return bool    true if this Voter supports the attribute, false otherwise
     */

    CONST APPROVE = "approve";
    CONST DISAPPROVE = "disapprove";

    public function supportsAttribute($attribute)
    {
        return(in_array($attribute,array(
                self::APPROVE,
                self::DISAPPROVE
            )
        ));
    }

    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return bool    true if this Voter can process the class
     */
    public function supportsClass($class)
    {
        $class = get_class($class);
        $supportsClass = Demand::class;
        return($class===$supportsClass);
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token A TokenInterface instance
     * @param Demand $demand
     * @param array $attributes An array of attributes associated with the method being invoked
     *
     * @throws \InvalidArgumentException
     * @internal param null|object $object The object to secure
     * @return int     either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $demand, array $attributes)
    {
        if(!$this->supportsClass($demand))
            return VoterInterface::ACCESS_ABSTAIN;

        if(1 !== count($attributes))
            throw new \InvalidArgumentException("Only one attribute is allowed");

        $attribute = $attributes[0];
        if(!$this->supportsAttribute($attribute))
            return VoterInterface::ACCESS_ABSTAIN;

        /** @var Employee $employee */
        $employee = $token->getUser();

//        if(! $employee instanceof UserInterface)
//            return VoterInterface::ACCESS_DENIED;

        if($attribute === self::APPROVE  || $attribute === self::DISAPPROVE)
        {
            if($demand->getTargetEmployee()===$employee || strcmp($employee->getRole(),"ROLE_ADMIN"))
            {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }

}