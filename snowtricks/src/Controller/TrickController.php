<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Message;
use App\Form\MessageType;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Service\Handler\ImagesHandler;
use App\Service\Handler\VideoHandler;
use App\Service\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\AjaxDataCollector;
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
            ->findBy([], ['id' => 'DESC'], $limit = 6 , $offset = 0);

        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks,
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

            return $this->redirectToRoute('index');
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
        $images = $trick->getImages();
        $videos = $trick->getVideos();
        $message = new Message();
        $messages = $this->getDoctrine()->getRepository(Message::class)->findBy(
            [
                'trick' => $trick
            ],
            [
                'id' => 'DESC'
            ],
            $limit = 4
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

        dump($message);
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'messages' => $messages,
            'images' => $images,
            'videos' => $videos,
            'form' => $form->createView()
        ]);
    }



    /**
     * @var Request $request
     * @var Trick   $trick
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
     * @param Request       $request       Request
     * @param Trick         $trick         Trick entity
     * @param VideoHandler  $videoHandler  Video Handler
     * @param ImagesHandler $imagesHandler Images Handler
     *
     * @return Response
     *
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Trick $trick, VideoHandler $videoHandler, ImagesHandler $imagesHandler): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        $images_path = $this->getParameter('images_path');
        $images = $trick->getImages();
        $videos = $trick->getVideos();
        if ($form->isSubmitted() && $form->isValid()) {
            $imagesHandler->addImages($trick, $images_path);
            $videoHandler->addVideos($trick);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('index');
        }
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'images' => $images,
            'videos' => $videos,
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
     * @Route("/supprimer/{id}", name="trick_delete", methods={"DELETE"})
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

        return $this->redirectToRoute('index');
    }

    /**
     * @param TrickRepository $repository Trick repository
     * @param Request         $request    ServerRequest
     *
     * @return Response
     * @Route("/trick/show_more/{id}", name="show_more")
     */
    public function showMore(TrickRepository $repository, Request $request)
    {
        $last_id = $request->get('id');
        $tricks = $repository->showMore($last_id);
        $size = count($tricks);


       return $this->render('trick/new_trick.html.twig', ['tricks' => $tricks, 'size' => $size]);
    }


}
