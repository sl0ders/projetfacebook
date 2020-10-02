<?php


namespace App\Controller;

use App\Repository\FriendshipRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LayoutBundleController extends AbstractController
{
    public function headerAction(UserRepository $userRepository)
    {
        $friendsRequestsNotAccepted = $userRepository->findUserRequestNotAccepted($this->getUser());
        $userRequestsNotAccepted = $userRepository->findFriendRequestNotAccepted($this->getUser());
        $friends = $userRepository->findFriend($this->getUser());

        return $this->render('layout/_header.html.twig', [
            "userNotAccepted" => $userRequestsNotAccepted,
            "myFriendsRequestNotAccepted" => $friendsRequestsNotAccepted,
            "friends" => $friends
        ]);
    }

    public function leftMenuAction()
    {
        return $this->render('layout/_leftSide.html.twig');
    }

    public function rightMenuAction()
    {
        return $this->render('layout/_rightSide.html.twig');
    }
}
