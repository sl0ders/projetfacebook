<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use LogicException;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/signup",name="app_signup")
     * @param Request $request
     * @param Swift_Mailer $mailer
     * @return Response
     */
    public function signup(Request $request, Swift_Mailer $mailer)
    {
        $user = new User();
        $formSignup = $this->createForm(UserType::class, $user);
        $formSignup->handleRequest($request);
        if ($formSignup->isSubmitted() && $formSignup->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setRoles(["ROLE_USER"]);
            $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setIsValidate(false);
            $em->persist($user);
            $em->flush();
            $message = (new Swift_Message('Email de validation de compte de la part de NetLink'))
                ->setFrom('sl0ders@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/registration.html.twig',
                        [
                            'name' => $user->getFirstname()
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);
            $this->addFlash("success", "Un email vient de vous etre envoyé pour valider votre adresse email");
            return $this->redirectToRoute("app_login");
        } else {
            $this->addFlash("success", "Une erreur a eu lieu l'ors de votre enregistrement");
        }

        return $this->render("security/signup.html.twig", [
            "form" => $formSignup->createView()
        ]);
    }
}
