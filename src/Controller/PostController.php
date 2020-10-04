<?php


namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\UserRepository;
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
     * @Route("/show/{id}" ,name="post_show", requirements={"id":"\d+"})
     * @param Post $post
     * @param UserRepository $userRepository
     * @return Response
     */
    public function show(Post $post, UserRepository $userRepository)
    {
        $friends = $userRepository->findFriend($this->getUser());
        return $this->render("post/show.html.twig", [
            "post" => $post,
            "friends" => $friends
        ]);
    }


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
        if ($formPost->isSubmitted() && $formPost->isValid()) {
            $post->setUpdatedAt(new DateTime());
            $em->persist($post);
        }
        $em->flush();
        return $this->render("post/edit.html.twig", [
            "form" => $formPost->createView()
        ]);
    }

    /**
     * @Route("/share/{post}-{user}", name="post_share", requirements={"id":"\d+"})
     * @param Post $post
     * @param User $user
     * @return RedirectResponse
     */
    public function share(Post $post, User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $notif = new Notification();
        $notif->setPost($post)
            ->setReceiver($user)
            ->setSender($this->getUser())
            ->setCreatedAt(new DateTime());
        $em->persist($notif);
        $em->flush();
        $userName = $user->fullname();
        $this->addFlash("success", "$userName a bien été notifié");
        return $this->redirectToRoute("home");
    }
}
