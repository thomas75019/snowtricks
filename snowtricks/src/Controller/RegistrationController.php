<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use App\Service\email\ActivationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

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
        \Swift_Mailer $mailer
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            $message = (new \Swift_Message('Activate you account'))
                ->setFrom('tlarousse3@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('email/activate_account.html.twig',
                        [
                            'token' => $user->getActivationToken()
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
