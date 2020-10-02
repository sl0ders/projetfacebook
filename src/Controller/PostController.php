<?php


namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PostController
 * @package App\Controller
 *
 * @Route("/post")
 */
class PostController extends abstractController
{
    /**
     * @Route("/delete/{id}" ,name="post_delete", methods={"GET","POST","DELETE"}, requirements={"id":"\d+"})
     * @param Post $post
     * @return RedirectResponse
     */
    public function delete(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($post);
        $em->flush();
        $this->addFlash("success", "Votre article a bien été supprimé");
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/edit/{id}" ,name="post_edit", methods={"GET","POST"}, requirements={"id":"\d+"})
     * @param Post $post
     * @param Request $request
     * @return Response
     */
    public function edit(Post $post, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $formPost = $this->createForm(PostType::class, $post);
        $formPost->handleRequest($request);
        if($formPost->isSubmitted() && $formPost->isValid()) {
            $post->setUpdatedAt(new DateTime());
            $em->persist($post);
        }
        $em->flush();
        return $this->render("post/edit.html.twig", [
            "form" => $formPost->createView()
        ]);
    }
}
