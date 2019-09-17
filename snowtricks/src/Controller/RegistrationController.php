<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Service\email\SecurityEmail;
use App\Service\Handler\ImagesHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;


/**
 * Class RegistrationController
 *
 * @package App\Controller
 */
class RegistrationController extends AbstractController
{
    /**
     * @param Request                      $request         ServerRequest
     * @param UserPasswordEncoderInterface $passwordEncoder Password Encoder
     * @param GuardAuthenticatorHandler    $guardHandler    Guard Handler
     * @param LoginFormAuthenticator       $authenticator   Authenticator
     * @param \Swift_Mailer                $mailer          Mailer
     *
     * @return Response
     *
     * @Route("/register", name="app_register")
     */
    public function register
    (
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        \Swift_Mailer $mailer,
        ImagesHandler $avatar
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $avatar_path = $this->getParameter('avatar_path');
            $file = $form['photo']->getData();
            $avatar->addAvatar($user, $file ,$avatar_path);



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $body = $this->renderView('email/activate_account.html.twig',
                [
                    'token' => $user->getActivationToken()
                ]
            );

            $email = new SecurityEmail($mailer, $user->getEmail(), $body, 'Activer votre compte');

            $email->send();

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        dump($user);
        
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
