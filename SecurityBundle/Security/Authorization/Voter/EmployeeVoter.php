<?php
/**
 * Created by PhpStorm.
 * User: Paja
 * Date: 5.9.14.
 * Time: 15.17
 */

namespace Devana\SecurityBundle\Security\Authorization\Voter;


use Devana\DoctrineBundle\Entity\Shift;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Devana\DoctrineBundle\Entity\Employee;

class EmployeeVoter implements VoterInterface {

    CONST EDIT = 'edit';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::EDIT,
        ));
    }

    public function supportsClass($class)
    {
        $class = get_class($class);
        $supportsClass = Shift::class;
        return $supportsClass==$class ;
    }

    public function vote(TokenInterface $token, $shift, array $attributes)
    {
        if(!$this->supportsClass($shift))
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

        if($attribute == self::EDIT)
        {
            if(strcmp($employee->getRole(),"ROLE_ADMIN")===0)
            {
                    return VoterInterface::ACCESS_GRANTED;
            }

        }

        return VoterInterface::ACCESS_DENIED;

    }

} 