<?php

namespace App\Controller;

use App\Entity\User;
use function Symfony\Component\Debug\Tests\testHeader;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Class SecurityController
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
     * @return Response
     *
     * @Route("/activate/{token}", name="activate_account")
     */
    public function activateAccount(Request $request) : Response
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
}
