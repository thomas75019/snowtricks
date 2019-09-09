<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\Image1Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/image")
 */
class ImageController extends AbstractController
{
    /**
     * @param Request    $request    Request
     * @param Image      $image      Image Entity
     * @param Filesystem $filesystem Filesystem
     *
     * @return RedirectResponse
     *
     * @Route("/{id}/{trick_id}", name="image_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Image $image, Filesystem $filesystem): RedirectResponse
    {
        $trick_id = $request->get('trick_id');
        $image_path = $this->getParameter('images_path');

        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $filesystem->remove($image_path . '/'  .$image->getName());
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_edit', [
            'id' => $trick_id
        ]);
    }
}
