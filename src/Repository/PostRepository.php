<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\DocBlock\Tags\Author;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function postWithReaction()
    {
        $qb = $this->createQueryBuilder("p")
            ->leftJoin("p.reactions", "reaction")->addSelect("reaction");

        return $qb->getQuery()->getResult();
    }

    public function findByFriendConnectedUser($user)
    {
        $qb = $this->createQueryBuilder("p");
        $qb->leftJoin("p.author", "author")->addSelect("author")
            ->leftJoin("author.friends", "friends")->addSelect('friends')
            ->leftJoin("author.friendsWithMe", "friendsWithMe")->addSelect('friendsWithMe')
            ->leftJoin('p.comments', "comments")->addSelect("comments")
            ->addOrderBy("comments.id", "ASC")
            ->andWhere("friends.friend = :user")
            ->orWhere("friends.user = :user")
            ->orWhere('friendsWithMe.user = :user')
            ->andWhere("friends.is_pending = true")
            ->setParameter("user", $user)
            ->orderBy("p.createdAt", "DESC");
        return $qb->getQuery()->getResult();
    }
}
