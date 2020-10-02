<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function get_class;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     * @param UserInterface $user
     * @param string $newEncodedPassword
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function search($lastname)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.lastname LIKE :lastname')
            ->orWhere('u.firstname LIKE :lastname')
            ->setParameter('lastname', '%' . $lastname . '%')
            ->getQuery()
            ->execute();
    }

    public function findUserRequestNotAccepted($user)
    {
        $qb = $this->createQueryBuilder("u")
            ->leftJoin("u.friends", "friends")->addSelect("friends")
            ->leftJoin("u.friendsWithMe", "friendsWithMe")->addSelect("friendsWithMe")
            ->where('friends.friend = :user')
            ->andWhere("friends.is_pending = false")
            ->setParameter("user", $user);
        return $qb->getQuery()->getResult();
    }

    public function findFriendRequestNotAccepted($user)
    {
        $qb = $this->createQueryBuilder("u")
            ->leftJoin("u.friendsWithMe", "friendsWithMe")->addSelect("friendsWithMe")
            ->where('friendsWithMe.user = :user')
            ->andWhere("friendsWithMe.is_pending = false")
            ->setParameter("user", $user);
        return $qb->getQuery()->getResult();
    }

    public function findFriend($user)
    {
        $qb = $this->createQueryBuilder("u")
            ->leftJoin("u.friendsWithMe", "friendsWithMe")->addSelect("friendsWithMe")
            ->leftJoin("u.friends", "friends")->addSelect("friends")
            ->where('friends.friend = :user')
            ->orWhere('friendsWithMe.user = :user')
            ->andWhere("friendsWithMe.is_pending = true")
            ->setParameter("user", $user);
        return $qb->getQuery()->getResult();
    }
}
