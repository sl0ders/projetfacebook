<?php

namespace App\Repository;

use App\Entity\Friendship;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Friendship|null find($id, $lockMode = null, $lockVersion = null)
 * @method Friendship|null findOneBy(array $criteria, array $orderBy = null)
 * @method Friendship[]    findAll()
 * @method Friendship[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FriendshipRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Friendship::class);
    }

    public function findAllByConnectedUser($getUser)
    {
        $qb = $this->createQueryBuilder("friendship");
        $qb->andWhere('friendship.user = :user');
        $qb->orWhere('friendship.friend = :user');
        $qb->andWhere("friendship.is_pending = true")
        ->setParameter('user', $getUser);
        return $qb->getQuery()->getResult();
    }
}
