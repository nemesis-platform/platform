<?php
/**
 * Created by PhpStorm.
 * User: Pavel Batanov <pavel@batanov.me>
 * Date: 14.08.2014
 * Time: 11:10
 */

namespace NemesisPlatform\Core\Account\Security;

use Doctrine\ORM\EntityManagerInterface;
use NemesisPlatform\Core\Account\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container, EntityManagerInterface $em)
    {
        $this->container = $container;
        $this->em        = $em;
    }

    /** {@inheritdoc} */
    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByUsername($user->getUsername());
    }

    /** {@inheritdoc} */
    public function loadUserByUsername($username)
    {
        $user = $this->em->getRepository(User::class)
                         ->createQueryBuilder('user')
                         ->select(
                             'user',
                             'sdata',
                             'phones',
                             'phone',
                             'team',
                             'members',
                             'm_user',
                             'm_phone',
                             'm_phones'
                         )
                         ->leftJoin('user.participations', 'sdata')
                         ->leftJoin('user.phones', 'phones')
                         ->leftJoin('user.phone', 'phone')
                         ->leftJoin('sdata.teams', 'team')
                         ->leftJoin('team.members', 'members')
                         ->leftJoin('members.user', 'm_user')
                         ->leftJoin('m_user.phones', 'm_phones')
                         ->leftJoin('m_user.phone', 'm_phone')
                         ->where('user.email = :email')->setParameter('email', $username)
                         ->getQuery()->getOneOrNullResult();

        if (!$user) {
            throw new UsernameNotFoundException();
        }

        $roles = [];

        if ($user->isEnabled()) {
            $roles[] = 'ROLE_USER';
        }
        if ($user->isFrozen()) {
            $roles[] = 'ROLE_FROZEN';
        }
        if ($user->getPhone()) {
            $roles[] = 'ROLE_CONFIRMED_PHONE';
        }

        if (count($user->getParticipations()) > 0) {
            $roles[] = 'ROLE_MEMBER';
        }

        if (in_array($user->getUsername(), $this->container->getParameter('admin_usernames'), true)) {
            $roles[] = 'ROLE_ADMIN';
        }
        $user->setRoles($roles);

        return $user;
    }

    /** {@inheritdoc} */
    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
