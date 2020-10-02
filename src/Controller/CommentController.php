<?php


namespace App\Controller;


use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CommentController
 * @package App\Controller
 *
 * @Route("/comment")
 */
class CommentController extends abstractController
{
    /**
     * @Route("/{id}", name="comment_index")
     * @param Post $post
     * @return Response
     */
    public function index(Post $post)
    {
        //Create FormView of comment
        $formComment = $this->createForm(CommentType::class);

        return $this->render("comment/index.html.twig", [
                "formComment" => $formComment->createView(),
                "post" => $post
            ]
        );
    }


    /**
     * @Route("/add/{id}", name="comment_add")
     * @param Request $request
     * @param Post $post
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function addComment(Request $request, Post $post, UserRepository $userRepository)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $user = $userRepository->find($this->getUser());
            $comment
                ->setCreatedAt(new DateTime())
                ->setAuthor($user)
                ->setPost($post)
                ->setParentId(0)
                ->setIsEnabled(true);
            $em->persist($comment);
            $em->flush();
            $this->addFlash("success", "le commentaire a bien été posté");
            return $this->redirectToRoute("home");
        }
        $this->addFlash("warning", "Une erreur c'est produite lors de l'envoi du commentaire");
        return $this->redirectToRoute("home");
    }
}
