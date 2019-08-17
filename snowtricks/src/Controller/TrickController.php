<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Service\Handler\ImagesHandler;
use App\Service\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'tricks' => $tricks,
        ]);
    }

    /**
     * @var Request       $request Request
     * @var Slugger       $slugger Slugger service
     * @var ImagesHandler $image
     *
     * @return Response
     *
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     * @Template()
     */
    public function new(Request $request, Slugger $slugger, ImagesHandler $handler): Response
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
            $handler->addImages($trick, $images_path);

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
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    /**
     * @var Request $request Request
     * @var Trick   $trick   Trick entity
     *
     * @return Response
     *
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $handler->deleteImages($trick->getImages(), $images_path);
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_index');
    }
}
