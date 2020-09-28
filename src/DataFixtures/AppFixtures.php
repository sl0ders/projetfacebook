<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Friend;
use App\Entity\Post;
use App\Entity\Reaction;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        $users = [];
        $posts = [];
        $faker = Factory::create("fr_FR");
        $admin = new User();
        $password = $this->encoder->encodePassword($admin, "258790");
        $admin
            ->setEmail('sl0ders@gmail.com')
            ->setFirtname("Quentin")
            ->setLastname("Sommesous")
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($password);
        $this->em->persist($admin);

        for ($i = 1; $i <= 30; $i++) {
            $user = new User();
            $user
                ->setRoles(['ROLE_USER'])
                ->setLastname($faker->lastName)
                ->setFirtname($faker->firstName)
                ->setEmail($faker->email)
                ->setPassword($faker->password);
            $this->em->persist($user);
            array_push($users, $user);
        }

        for ($i = 1; $i <= 30; $i++) {
            $post = new Post();
            $post
                ->setTitle($faker->text(25))
                ->setContent($faker->text(500))
                ->setCreatedAt(new \DateTime())
                ->setPicture($faker->imageUrl())
                ->setAuthor($faker->randomElement($users));
            $this->em->persist($post);
            array_push($posts, $post);
        }
        for ($i = 1; $i <= 30; $i++) {
            $comment = new Comment;
            $comment->setAuthor($faker->randomElement($users))
                ->setCreatedAt(new \DateTime())
                ->setContent($faker->text(300))
                ->setPost($faker->randomElement($posts))
                ->setAuthor($faker->randomElement($users));
            $this->em->persist($comment);
        }
        for($i = 1; $i <= count($users); $i++) {
            $friend = new Friend();
            $friend->addUser($faker->randomElement($users));
            $this->em->persist($friend);
        }
        for( $i = 1; $i <= 30; $i++){
            $reaction =new Reaction();
            $reaction->setPost($faker->randomElement($posts));
            /** @var Post $post */
            foreach($posts as $post) {
                $reaction->setUser($post->getAuthor());
            }
            $this->em->persist($reaction);
        }
       $this->em->flush();
    }
}
