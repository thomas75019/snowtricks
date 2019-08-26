<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use function Symfony\Component\Debug\Tests\testHeader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\email\SecurityEmail;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
/**
 * Class SecurityController
 *
 * @package App\Controller
 */
class SecurityController extends AbstractController
{
    /**
     * @param AuthenticationUtils $authenticationUtils
     *
     * @Route("/login", name="app_login")
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $this->redirectToRoute('trick_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @param Request $request ServerRequest
     *
     * @return RedirectResponse
     *
     * @Route("/activate/{token}", name="activate_account")
     */
    public function activateAccount(Request $request) : RedirectResponse
    {
        $token = $request->get('token');

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy([
            'activation_token' => $token
        ]);

        $user->setIsActivated(true);
        $this->getDoctrine()->getManager()->flush();
        $this->addFlash('success', 'Votre compte à bien été activé');

        return $this->redirectToRoute('trick_index');
    }

    /**
     * @throws \Exception
     *
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/lost-password", name="lost_password", methods={"GET"})
     */
    public function lostPassword() : Response
    {
        return $this->render('security/lost_email.html.twig');

    }

    /**
     * @Route("/security/new-password", name="reinitialise_password_email", methods={"POST"})
     */
    public function sendReinitialisePassword(Request $request, SessionInterface $session, \Swift_Mailer $mailer)
    {
        $email = $request->request->get('email');
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user)
        {
            $this->addFlash('notice', 'L\'email n\'est liée avec aucun compte');
            return $this->redirectToRoute('lost_password');
        }

        $token = md5(uniqid());

        $session->set('token', $token);

        $body = $this->renderView('security/change_password.html.twig',
            [
                'token' => $session->get('token'),
                'id' => $user->getId()
            ]
        );

        $email = new SecurityEmail($mailer, $user->getEmail(), $body, 'Réinitialisé votre mot de passe');

        $email->send();

        $this->addFlash('notice', 'Un email pour réinitialiser votre mot de passe a été envoyé');

        return $this->redirectToRoute('trick_index');
    }

    /**
     * @var Request          $request
     * @var SessionInterface $session
     *
     * @return \Error|RedirectResponse|Response
     *
     * @Route("/change/password/{token}/{id}", name="change_password", methods={"GET","POST"})
     */
    public function changePassword(Request $request, SessionInterface $session)
    {
        $token = $request->get('token');
        $user = $this->getDoctrine()->getRepository(User::class)
            ->findOneBy(['id' => $request->get('id')]);

        /*if ($request->isMethod('post'))
        {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setPassword($request->get('newPassword'));
            $entityManager->flush();

            $session->remove('token');

            $this->addFlash('success', 'Votre mot de passe a bien été changé');

            return $this->redirectToRoute('trick_index');

        }*/
        if ($session->get('token') === $token && $request->isMethod('get'))
        {
            return $this->render('security/change_password_form.html.twig', ['token' => $token, 'id' => $user->getId()]);
        }

        return $this->redirectToRoute('lost_password');
    }


    /**
     * @return RedirectResponse
     *
     * @Route("/new/password/{id}", name="new_password", methods={"POST"})
     */
    public function newPassword(SessionInterface $session, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(User::class)
            ->findOneBy(['id' => $request->get('id')]);

        if ($request->isMethod('post'))
        {
            $plainPassword = $request->request->get('newPassword');
            $newPassword = $passwordEncoder->encodePassword($user, $plainPassword);
            $entityManager = $this->getDoctrine()->getManager();
            $user->setPassword($newPassword);
            $entityManager->flush();

            $session->remove('token');

            $this->addFlash('success', 'Votre mot de passe a bien été changé');

            return $this->redirectToRoute('trick_index');

        }


        $this->addFlash('erreur', 'erreur');

        return $this->redirectToRoute('trick_index');
    }


}
