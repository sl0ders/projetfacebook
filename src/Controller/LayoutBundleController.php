<?php


namespace App\Controller;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LayoutBundleController extends AbstractController
{
    public function headerAction(UserRepository $userRepository, NotificationRepository $notificationRepository)
    {
        $friendsRequestsNotAccepted = $userRepository->findUserRequestNotAccepted($this->getUser());
        $userRequestsNotAccepted = $userRepository->findFriendRequestNotAccepted($this->getUser());
        $friends = $userRepository->findFriend($this->getUser());
        $userNotifications = $notificationRepository->findBy(["receiver" => $this->getUser()]);
        return $this->render('layout/_header.html.twig', [
            "userNotAccepted" => $userRequestsNotAccepted,
            "myFriendsRequestNotAccepted" => $friendsRequestsNotAccepted,
            "friends" => $friends,
            "userNotifications" => $userNotifications
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
