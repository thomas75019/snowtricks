<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Message;
use App\Form\MessageType;
use App\Form\TrickType;
use App\Service\Handler\ImagesHandler;
use App\Service\Handler\VideoHandler;
use App\Service\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class TrickController extends AbstractController
{
    /**
     * @return Response
     *
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(): Response
    {
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findBy([], ['id' => 'DESC'], $limit = 10 , $offset = 0);


        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @var Request       $request Request
     * @var Slugger       $slugger Slugger service
     * @var ImagesHandler $images  Image Handler service
     * @var VideoHandler  $videos  Video Handler service
     *
     * @return Response
     *
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     * @Template()
     */
    public function new(Request $request, Slugger $slugger, ImagesHandler $images, VideoHandler $videos): Response
    {
        $trick = new Trick();
        $images_path = $this->getParameter('images_path');

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugger->slugger($trick->getName());

            $trick->setSlug($slug);
            $trick->setUser($this->getUser());
            $images->addImages($trick, $images_path);
            $videos->addVideos($trick);

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouveau trick à bien été ajouté');

            return $this->redirectToRoute('trick_index');
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @var Trick $trick Trick Entity
     *
     * @return Response
     *
     * @Route("/{slug}", name="trick_show", methods={"GET"})
     */
    public function show(Trick $trick): Response
    {
        $message = new Message();
        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(
            [
                'trick' => $trick
            ],
            [
                'id' => 'DESC'
            ]
        );

        $form = $this->createForm(MessageType::class, $message,
            [
                'action' => $this->generateUrl('message_new',
                    [
                        'slug' => $trick->getSlug()
                    ]
                )
            ]
        );
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'messages' => $messages,
            'form' => $form->createView()
        ]);
    }



    /**
     * @var Request $request
     *
     * @return Response
     *
     * @Route("/{slug}/new/message", name="message_new", methods={"GET","POST"})
     */
    public function newMessage(Request $request, Trick $trick): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $message->setTrick($trick);
            $message->setUser($this->getUser());
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('trick_show', ['slug' => $trick->getSlug()]);
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @var Request $request Request
     * @var Trick   $trick   Trick entity
     *
     * @return Response
     *
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Trick $trick, VideoHandler $video): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $video->addVideos($trick);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('trick_index');
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @var Request       $request Request
     * @var Trick         $trick   Trick entity
     * @var ImagesHandler $handler Images Handler
     *
     * @return RedirectResponse
     *
     * @Route("/{id}", name="trick_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Trick $trick, ImagesHandler $handler): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $images_path = $this->getParameter('images_path');
            $entityManager = $this->getDoctrine()->getManager();
            $handler->deleteImages($trick, $images_path);
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_index');
    }
}
