<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Form\MessageType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MessageRepository;


class MessageController extends AbstractController
{
    /**
     * @var Trick $trick
     *
     * @return object[]
     */
    public function getAll(Trick $trick)
    {
        $messages = $this->getDoctrine()
            ->getRepository(Message::class)
            ->findBy(
                [
                    'trick' => $trick
                ],
                'DESC',
                $limit = 4,
                $offset = 0
            );

        return $messages;
    }

    /**
     * @var Request $request
     * @var Trick   $trick
     *
     * @return Response
     *
     * @Route("/new/message", name="message_new_test", methods={"GET","POST"})
     */
    public function new(Request $request, Trick $trick): Response
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

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @var Message $message
     *
     * @return Response
     *
     * @Route("/{id}/message", name="message_show", methods={"GET"})
     */
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
        ]);
    }

    /**
     * @var Request $request
     * @var Message $message
     *
     * @return Response
     *
     * @Route("/{id}/edit/message", name="message_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @var Request $request
     * @var Message $message
     *
     * @return Response
     *
     * @Route("/{id}/message", name="message_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('message_index');
    }

    /**
     * @param MessageRepository $repository
     * @param Request           $request
     *
     * @return Response
     *
     * @Route("trick/message/show_more/{id}/{trick_id}", name="show_more_messages")
     */
    public function showMore(MessageRepository $repository, Request $request) : Response
    {
        $last_id = $request->get('id');
        $trick_id = $request->get('trick_id');
        $messages = $repository->showMore($last_id, $trick_id);
        $size = count($messages);

        return $this->render('message/new_messages.html.twig', ['messages' => $messages, 'size' => $size]);
    }
}
