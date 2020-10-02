<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Reaction;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\PostType;
use App\Form\SearchingType;
use App\Repository\PostRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="home")
     * @param PostRepository $postRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     * @return RedirectResponse|Response
     */
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request, UserRepository $userRepository, SessionInterface $session)
    {
        $users = [];

        //if no user is logged in, or returns to the login page
        if (!$this->getUser()) {
            return $this->redirectToRoute("app_login");
        }

        /** start FormView */

        // new search form
        $formSearch = $this->createForm(SearchingType::class);
        $formSearch->handleRequest($request);
        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $user = $formSearch->getData()->getFirstname();
            $users = $userRepository->search($user);
            if ($users == null) {
                $this->addFlash('erreur', 'Aucun article contenant ce mot clé dans le titre n\'a été trouvé, essayez en un autre.');
            }
        }

        // new Post form
        $post = new Post();
        $formPost = $this->createForm(PostType::class, $post);
        $formPost->handleRequest($request);
        if ($formPost->isSubmitted() && $formPost->isValid()) {
            $post->setCreatedAt(new \DateTime());
            $post->setAuthor($this->getUser());
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash("success", "Votre article a bien été publier");
            return $this->redirectToRoute("home");
        }
        // formView of comment
        $formComment = $this->createForm(CommentType::class);
        $formComment->handleRequest($request);
        $formComment->handleRequest($request);
        if ($formComment->isSubmitted()) {
            return $this->redirectToRoute("comment_add");
        }

        /** end Form */

        // Recovery of all post of connected friend and mine
        $posts = $postRepository->findByFriendConnectedUser($this->getUser());
        // Paginate posts result
        $pagePost = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            20
        );

        return $this->render("index.html.twig", [
            'formComment' => $formComment->createView(),
            'form' => $formPost->createView(),
            'formSearch' => $formSearch->createView(),
            "posts" => $pagePost,
            "users" => $users
        ]);
    }

    /**
     *
     * @Route("/like/{post}/{user}", name="post_like")
     * @param Post $post
     * @param User $user
     * @return RedirectResponse
     */
    public function like(Post $post, User $user)
    {
        $reaction = new Reaction();
        $reaction->setPost($post);
        $reaction->setUser($user);
        $this->em->persist($reaction);
        $this->em->flush();
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/unLike/{post}/{user}", name="post_unLike")
     * @param Post $post
     * @param User $user
     * @return RedirectResponse
     */
    public function unLiked(Post $post, User $user)
    {
        $reaction = $this->em->getRepository(Reaction::class)->findOneBy(["post" => $post, "user" => $user]);
        $this->em->remove($reaction);
        $this->em->flush();
        return $this->redirectToRoute("home");
    }
}
