<?php

namespace App\Entity;
use App\Repository\FriendshipRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FriendshipRepository::class)
 */
class Friendship
{
    /**
     * @ORM\Id
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friends")
     * @ORM\Id
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="friendsWithMe")
     * @ORM\Id
     */
    public $friend;

    /**
     * Example of an additional attribute.
     *
     * @ORM\Column(type="boolean")
     */
    private $is_pending;


    public function getId(): ?int
{
    return $this->id;
}

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getFriend()
    {
        return $this->friend;
    }

    /**
     * @param mixed $friend
     */
    public function setFriend($friend): void
    {
        $this->friend = $friend;
    }

    /**
     * @return mixed
     */
    public function getIsPending()
    {
        return $this->is_pending;
    }

    /**
     * @param mixed $is_pending
     */
    public function setIsPending($is_pending): void
    {
        $this->is_pending = $is_pending;
    }
}
