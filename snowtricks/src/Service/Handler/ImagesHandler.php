<?php

namespace App\Service\Handler;

use App\Entity\Trick;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ImagesHandler
 *
 * Handle the images
 *
 * @package App\Service\Handler
 */
class ImagesHandler
{
    /**
     * @var Filesystem $filesystem
     */
    public $filesystem;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     *
     * Handle images and set their name
     *
     * @param Trick $trick
     * @param $images_path
     *
     * @return void
     */
    public function addImages(Trick $trick, $images_path) : void
    {
        foreach ($trick->getImages() as $image) {
            if($image->getName() == null  && $image->getFile() != null){
                $file = $image->getFile();
                $fileName = $trick->getName(). '-'. uniqid() .'.'.$file->guessExtension();
                $file->move(
                    $images_path,
                    $fileName
                );
                $image->setName($fileName);
                $image->setTrick($trick);
            }
        }
        return;
    }


    /**
     * @param $trick      $trick
     * @param $image_path
     *
     * @return void
     */
    public function deleteImages(Trick $trick, $image_path) : void
    {
        foreach ($trick->getImages() as $image) {
            $this->filesystem->remove($image_path . '/'  .$image->getName());
        }

        return;
    }

}