<?php
/**
 * Created by PhpStorm.
 * User: Paja
 * Date: 26.8.14.
 * Time: 15.04
 */

namespace Devana\SecurityBundle\Auth;


use Devana\DoctrineBundle\Entity\Employee;
use Doctrine\ORM\EntityManager;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class OAuthProvider extends OAuthUserProvider
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function loadUserByUsername($email)
    {
        $userRepository = $this->entityManager->getRepository('DevanaDoctrineBundle:Employee');
        $user = $userRepository->findOneBy(['email' => $email]);
        return $user;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $email = $response->getEmail();
        // TODO: === 0
        if (preg_match("/@managewp\.com$/", $email) === null) {
            throw new UsernameNotFoundException("Unsupported authentication with non Devana employees");
        }

        $user = $this->loadUserByUsername($email);



        if ($user === null) {
            $user = new Employee();
            $user->setEmail($email);
            $responseArray = $response->getResponse();
            $user->setName($responseArray['given_name']);
            $user->setSurname($responseArray['family_name']);
            $user->setRole("ROLE_USER");
//            $user->setName($response->getRealName());
            $this->entityManager->persist($user);
            try {
                $this->entityManager->flush();
            } catch (\Exception $e) {
                throw new UsernameNotFoundException("Could not persist user");
            }
        }

        return $user;
    }

    public function supportsClass($class)
    {
        return $class === Employee::class;
    }


}