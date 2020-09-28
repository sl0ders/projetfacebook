<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @return RedirectResponse|Response
     */
    public function index(PostRepository $postRepository, PaginatorInterface $paginator, Request $request)
    {
        $post = new Post();
        $formPost = $this->createForm(PostType::class,$post);
        $formPost->handleRequest($request);
        if ($formPost->isSubmitted() && $formPost->isValid()){
            $post->setCreatedAt(new DateTime());
            $post->setAuthor($this->getUser());
            $this->em->persist($post);
            $this->em->flush();
            $this->addFlash("success", "Votre article a bien été publier");
            return $this->redirectToRoute("home");
        }

        if (!$this->getUser()) {
            return $this->redirectToRoute("app_login");
        }
        $posts = $postRepository->findAll();
        $pagePost = $paginator->paginate(
            $posts,
            $request->query->getInt('page', 1),
            20
        );
        return $this->render("index.html.twig", [
            "posts" => $pagePost,
            'form' => $formPost->createView()
        ]);
    }
}
