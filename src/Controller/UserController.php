<?php


namespace App\Controller;


use App\Entity\Friendship;
use App\Entity\User;
use App\Repository\FriendshipRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 *
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/",name="user_index")
     */
    public function index()
    {
        $this->render("user/index.html.twig");
    }

    /**
     * @Route("/show/{id}",name="user_show")
     * @param User $user
     * @param FriendshipRepository $friendshipRepository
     * @return Response
     */
    public function show(User $user, FriendshipRepository $friendshipRepository)
    {
        $friendly = false;
        $friendshipExist = $friendshipRepository->findOneBy(["user" => $this->getUser(), "friend" => $user]);
        $usershipExist = $friendshipRepository->findOneBy(["user" => $user, "friend" => $this->getUser()]);
        if ($friendshipExist or $usershipExist) {
            $friendly = true;
        }
        return $this->render("user/show.html.twig", [
            "user" => $user,
            'friendly' => $friendly
        ]);
    }

    /**
     * @param User $friend
     * @param FriendshipRepository $friendshipRepository
     *
     * @Route("/acceptFriend/{friend}", name="friend_accept")
     */
    public function acceptFriend(User $friend, FriendshipRepository $friendshipRepository)
    {
        /** @var Friendship $friendship */
        $friendship = $friendshipRepository->findOneBy(["friend" => $friend, "user" => $this->getUser()]);

        $friendship->setIsPending(true);
        $em = $this->getDoctrine()->getManager();
        $em->persist($friendship);
        $em->flush();
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/connect/{id}",name="user_connect")
     * @param User $user
     * @param FriendshipRepository $friendshipRepository
     * @return RedirectResponse
     */
    public function userConnect(User $user, FriendshipRepository $friendshipRepository)
    {
        $friendshipExist = $friendshipRepository->findOneBy(["user" => $this->getUser(), "friend" => $user]);
        $usershipExist = $friendshipRepository->findOneBy(["user" => $user, "friend" => $this->getUser()]);
        if (isset($usershipExist)) {
            $this->addFlash("danger", "Cet utilisateur est deja votre ami");
            return $this->redirectToRoute("home");
        }
        if (!isset($friendshipExist)) {
            $friendship = new Friendship();
            $friendship->setUser($user);
            $friendship->setFriend($this->getUser());
            $friendship->setIsPending(0);
            $em = $this->getDoctrine()->getManager();
            $em->persist($friendship);
            $em->flush();
        } else {
            $this->addFlash("danger", "Cet utilisateur est deja votre ami");
        }
        return $this->redirectToRoute("home");
    }

    /**
     * @Route("/delete/{friend}",name="delete_friendship")
     * @param User $friend
     * @param FriendshipRepository $friendshipRepository
     */
    public function deleteFriendship(User $friend, FriendshipRepository $friendshipRepository){
        $em = $this->getDoctrine()->getManager();
        $friendshipUser = $friendshipRepository->findOneBy(["user" => $friend, "friend" => $this->getUser()]);
        $friendshipFriend = $friendshipRepository->findOneBy(["user" => $this->getUser(), "friend" => $friend]);
        $message = "Le lien d'amitié a bien été rompu";
        if ($friendshipUser) {
            $em->remove($friendshipUser);
        }
        if ($friendshipFriend){
            $em->remove($friendshipFriend);
        }

        $em->flush();
        $this->addFlash("success", $message);
        return $this->redirectToRoute("home");
    }
}
