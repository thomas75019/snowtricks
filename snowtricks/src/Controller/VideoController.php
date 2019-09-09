<?php

namespace App\Controller;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/video")
 */
class VideoController extends AbstractController
{

    /**
     * @param Request $request Request
     * @param Video   $video   Video
     *
     * @return RedirectResponse
     *
     * @Route("/{id}/{trick_id}", name="video_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Video $video): RedirectResponse
    {
        $trick_id = $request->get('trick_id');
        if ($this->isCsrfTokenValid('delete'.$video->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($video);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_edit', [
            'id' => $trick_id
        ]);
    }
}
